<?php

namespace mmerlijn\LaravelSalt\Models;

use BackedEnum;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Http\Resources\FlowResource;
use mmerlijn\LaravelSalt\Observers\FlowObserver;
use Workbench\Database\Factories\FlowFactory;


/**
 * @property null|FlowError $error
 * @property array $stack
 * @property Carbon $try_after
 * @property int $attempts
 * @property int $flow_error_id
 * @property int $payload_id
 * @property string $payload_type
 * @property int $type
 * @property Model|null $payload
 * @property array $data
 * @property string $request
 * @property Carbon $request_at
 * @property string $response
 * @property int $response_type
 * @property int $request_type
 * @property Carbon $response_at
 * @property bool $active
 * @property int $labtrain_id
 * @property int $patient_id
 * @property string $request_nr
 */
#[UseResource(FlowResource::class), ObservedBy(FlowObserver::class)]
class Flow extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'stack',
        'flow_error_id',
        'payload_id',
        'payload_type',
        'attempts',
        'try_after',
        'store',
        'data',
        'request',
        'response',
        'request_at',
        'response_at',
        'response_type',
        'request_type',
        'type',
        'patient_id',
        'request_nr',
        'labtrain_id',
        'active'
    ];
    protected $table = 'flows';

    protected function casts(): array
    {
        return [
            'stack' => 'array',
            'try_after' => 'datetime',
            'data' => 'array',
            'request_at' => 'datetime',
            'response_at' => 'datetime',
            'active' => 'boolean',
        ];
    }

    public function errors(): MorphMany
    {
        return $this->morphMany(FlowError::class, 'from');
    }

    public function error(): BelongsTo
    {
        return $this->belongsTo(FlowError::class, 'flow_error_id');
    }


    public function payload(): MorphTo
    {
        return $this->morphTo();
    }

    public static function add(int|BackedEnum $flow, null|array|Model $payload, $wait = 0, array $data = []): self
    {
        $stack = self::getStackFromConfig($flow?->value ?? $flow);
        if (!$stack) {
            $fe = FlowError::create([
                'level' => ErrorLevelEnum::MENNO,
                'from_type' => self::class,
                'from_id' => $payload?->id ?? null,
                'message' => "Flow $flow has no stack configured in laravel_salt.config",
                'notify' => true,

            ]);
        }
        if ($payload) {
            return Flow::updateOrCreate([
                'payload_id' => is_array($payload) ? null : $payload->id,
                'payload_type' => is_array($payload) ? null : $payload->getMorphClass(),
                'type' => $flow,
            ], [
                'stack' => $stack,
                'try_after' => now()->addMinutes($wait)->subSecond(),
                'attempts' => 0,
                'flow_error_id' => $fe->id ?? null,
                'active' => true,
                'data' => is_array($payload) ? $payload : $data
            ]);
        }
        return self::create([
            'type' => $flow,
            'stack' => $stack,
            'flow_error_id' => $fe->id ?? null,
            'try_after' => now()->addMinutes($wait)->subSecond(),
            'active' => true,
            'data' => $data
        ]);
    }

    public static function runAll(): void
    {
        foreach (self::whereNull('flow_error_id')
                     ->whereActive(false)
                     ->cursor() as $flow) {
            $flow->stack = self::getStackFromConfig($flow->type?->value ?? $flow->type);
            $flow->active = true;
            $flow->save();
        }
        foreach (self::whereNull('flow_error_id')
                     ->where('try_after', '<', now())
                     ->whereActive(true)
                     ->cursor() as $flow) {
            $flow->run();
        }
    }


    public function run(): void
    {
        if (empty($this->stack)) {
            $this->delete();
            return;
        }
        if ($this->flow_error_id) {
            return; //los eerst het probleem op
        }
        if ($this->try_after->isAfter(now())) {
            return;
        }

        $todo = $this->stack[0];
        if (!is_array($todo)) {
            $todo = [$todo];
        }
        foreach ($todo as $task) {
            $t = config('laravel_salt.tasks.' . $task, false);
            if ($t) {
                if (str_contains($t, 'Job')) {
                    $t::dispatch($this);

                } else {
                    new $t()($this);
                }
            } else {
                $this->flow_error_id =
                    FlowError::create([
                        'level' => ErrorLevelEnum::MENNO,
                        'from_type' => 'flow',
                        'from_id' => $this->id,
                        'message' => "Flow task $task has no class configured in laravel-salt.config",
                        'notify' => true,
                    ])->id;
                $this->save();
            }
        }
    }

    public function fail(
        int                    $wait = 0,
        null|\Error|\Exception $exception = null,
        int|array              $runFirst = 0,
        int|array              $runFirstAfterAttempts = 0,
        int                    $maxAttempts = 0,
        bool                   $reset = false,
        ?string                $solution = null,
        bool                   $stopAtException = true,
        bool                   $notify = true,
        bool                   $resetResponse = false,
        bool                   $resetRequest = false,
        int|ErrorLevelEnum     $errorLevel = 1,
        ?Model                 $errorAt = null,
        ?string                $errorClass = null,
        ?\Closure              $action = null): void
    {
        $this->attempts += 1;
        $this->nextAttemptAt(wait: $wait);
        if ($this->attempts > $maxAttempts) {
            $exception = $exception ?? new \Exception("Maximum number of attempts reached");
        }
        if ($exception) {
            $flowError = FlowError::updateOrCreate([
                'flow_id' => $this->id,
            ], [
                'level' => $errorLevel?->value ?? $errorLevel,
                'from_type' => $this->payload?->getMorphClass(),
                'from_id' => $this->payload?->primaryKey,
                'at_type' => $errorAt?->getMorphClass(),
                'at_id' => $errorAt?->primaryKey,
                'class' => $errorClass,
                'solution' => $solution,
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'notify' => $notify,
            ]);
            if ($stopAtException) { //this stops de runner
                $this->flow_error_id = $flowError->id;
            }
        }
        if ($resetRequest) {
            $this->resetRequest();
        }
        if ($resetResponse) {
            $this->resetResponse();
        }
        if ($reset) {
            $this->reset();
        }
        if ($runFirst) {
            if (!$runFirstAfterAttempts or $runFirstAfterAttempts > $this->attempts) {
                $this->prepend(task: $runFirst, wait: $wait); //reset the flow push task in front
            }
        }
        if ($action !== null) {
            //Indien je ook private functions zou willen aanroepen
            //$privateClosure = $action->bindTo($this,$this);
            //$privateClosure();
            //Alleen toegang tot Public functions
            $action($this);
        }
        $this->save();
        $this->run();
    }

    public function done(
        int|string $task,
        int        $wait = 0,
        int|array  $runNext = 0,
        int|array  $skipTask = 0,
    ): void
    {
        if (is_string($task)) {
            $task = array_find_key(config('laravel_salt.tasks', []), fn($item) => $item == $task);
        }
        //remove task from stack
        $this->stack = $this->filter_first_integer_recursive($this->stack, $task);

        if ($runNext) {
            $this->prepend(task: $runNext, wait: $wait);
        } else {
            $this->reset(wait: $wait);
        }

        if ($skipTask) {
            $this->stack = $this->filter_integer_recursive($this->stack, $skipTask);
        }
        $this->save();
        $this->run();

    }

    public function resetRequest(): void
    {
        $this->request_at = null;
        $this->request = null;
        $this->request_type = 0;
    }

    public function resetResponse(): void
    {
        $this->response_at = null;
        $this->response = null;
        $this->response_type = 0;
    }

    public function reset(int $wait = 0): void
    {
        $this->flow_error_id = null;
        $this->resetResponse();
        $this->resetRequest();
        $this->attempts = 0;
        $this->nextAttemptAt(wait: $wait);
    }


    public function prepend(int|array|BackedEnum $task, int $wait = 0): void
    {
        $this->reset(wait: $wait);
        $stack = $this->stack;
        array_unshift($stack, $task->value ?? $task);
        $this->stack = $stack;
    }


    private function nextAttemptAt($wait = 0): void
    {
        if ($wait) {
            $this->try_after = now()->addMinutes($wait);
            return;
        }
        if ($this->attempts <= 10) {
            $this->try_after = now()->addMinutes($this->attempts);
        }
        // exponentieel backoff na 10 tries, elke 1.5x langer wachten dan de vorige keer
        $this->try_after = now()->addMinutes((int)(10 * 1.5 ** ($this->attempts - 10)));
    }

    private function filter_first_integer_recursive(array $array, int $target, bool &$found = false): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // We geven &$found mee, zodat de diepere niveaus weten of er al iets is verwijderd
                $result[$key] = $this->filter_first_integer_recursive($value, $target, $found);
            } else {
                // Als we de target vinden én we hebben hem nog niet eerder gevonden...
                if ($value === $target && !$found) {
                    $found = true; // Markeer als gevonden, deze waarde wordt NIET toegevoegd
                    continue;
                }

                // In alle andere gevallen behouden we de waarde
                $result[$key] = $value;
            }
        }
        return array_values($result);
    }

    private function filter_integer_recursive(array $array, int $target): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $filteredArray = $this->filter_integer_recursive($value, $target);
                // Voeg de subarray alleen toe als deze na het filteren niet leeg is
                if (!empty($filteredArray)) {
                    $result[$key] = $filteredArray;
                }
            } elseif ($value !== $target) {
                $result[$key] = $value;
            }
        }
        return array_values($result);
    }

    private static function getStackFromConfig(int $flow): ?array
    {
        $stack = config('laravel_salt.flows.' . ($flow?->value ?? $flow), false);
        if (is_int($stack[0])) {
            return $stack;
        } else {
            if (is_int($stack[0][0] ?? false)) {
                return $stack;
            }
        }
        if ($flow > 8000) {
            return [8000]; //send only response/request (no return expected)
        }
        //TODO Als de stack Jobs bevat moeten deze nog even omgezet worden naar integers
        return null;

    }

    private function clearResponseAndRequest(): void
    {
        $this->response_at = null;
        $this->request_at = null;
        $this->response = null;
        $this->request = null;
        $this->save();
    }

    protected static function newFactory(): FlowFactory
    {
        return FlowFactory::new();
    }
}
