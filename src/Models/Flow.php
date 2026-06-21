<?php

namespace mmerlijn\LaravelSalt\Models;

use BackedEnum;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Enums\ErrorLevelEnum;
use mmerlijn\LaravelSalt\Helpers\Error;
use mmerlijn\LaravelSalt\Http\Resources\FlowResource;
use mmerlijn\LaravelSalt\Observers\FlowObserver;
use Workbench\Database\Factories\FlowFactory;


/**
 * @property null|AppError $error
 * @property array $stack
 * @property Carbon $try_after
 * @property int $attempts
 * @property int $app_error_id
 * @property int $payload_id
 * @property string $payload_type
 * @property int $type
 * @property Model|null $payload
 * @property array $data
 * @property int $exchange_id
 * @property FlowExchange $exchange
 */
#[UseResource(FlowResource::class), ObservedBy(FlowObserver::class)]
class Flow extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'stack',
        'app_error_id',
        'payload_id',
        'payload_type',
        'attempts',
        'try_after',
        'store',
        'data',
        'exchange_id'
    ];
    protected $table = 'flows';

    protected function casts(): array
    {
        return [
            'stack' => 'array',
            'try_after' => 'datetime',
            'data' => 'array',
        ];
    }

    public function exchange(): BelongsTo
    {
        return $this->belongsTo(FlowExchange::class, 'exchange_id');
    }

    public function appErrors(): MorphMany
    {
        return $this->morphMany(AppError::class, 'from');
    }

    public function error(): BelongsTo
    {
        return $this->belongsTo(AppError::class, 'app_error_id');
    }

    public function exchanges(): HasMany
    {
        return $this->hasMany(FlowExchange::class, 'flow_id');
    }

    public function payload(): MorphTo
    {
        return $this->morphTo();
    }

    public static function add(int|BackedEnum $flow, ?Model $payload, $wait = 0): self
    {
        $stack = self::getStackFromConfig($flow?->value ?? $flow);
        if (!$stack) {
            $ae = new Error(level: ErrorLevelEnum::MENNO, message: "Flow $flow has no configuration in laravel-salt.config", notify: true)->store();
        }
        if ($payload) {
            return $payload->flows()->create([
                'type' => $flow,
                'stack' => $stack,
                'try_after' => now()->addMinutes($wait)->subSecond(),
                'app_error_id' => $ae->id ?? null,
            ]);
        }
        return self::create([
            'type' => $flow,
            'stack' => $stack,
            'app_error_id' => $ae->id ?? null,
            'try_after' => now()->addMinutes($wait)->subSecond(),
        ]);
    }

    public static function runAll(): void
    {
        foreach (self::whereNull('app_error_id')
                     ->where('try_after', '<', now())
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
        if ($this->app_error_id) {
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
                $this->app_error_id = new Error(
                    level: ErrorLevelEnum::MENNO,
                    fromObject: $this,
                    message: "Flow task $task has no class configured in laravel-salt.config")
                    ->store()->id;
                $this->save();
            }
        }
    }

    public function retry(int $wait = 0): void
    {
        $this->attempts += 1;
        $this->wait(wait: $wait);
        $this->save();
    }

    public function fail(null|string|AppError $appError = null, int $wait = 0): void
    {
        $this->attempts++;
        $this->wait(wait: $wait);
        if ($appError instanceof AppError) {
            $this->app_error_id = $appError->id;
        } elseif (is_string($appError)) { //geen reden om te stoppen
            $data = $this->data;
            $data['error'] = $appError;
            $this->data = $data;
        }
        $this->save();
    }

    public function done(int|string $task, int $wait = 0): void
    {
        if (is_string($task)) {
            $task = array_find_key(config('laravel_salt.tasks', []), fn($item) => $item == $task);
        }
        //remove task from stack
        $this->stack = $this->filter_integer_recursive($this->stack, $task);
        $this->next(wait: $wait);
    }

    public function prepend(int|array|BackedEnum $task, int $wait = 0): void
    {
        $stack = $this->stack;
        array_unshift($stack, $task->value ?? $task);
        $this->stack = $stack;
        $this->wait(wait: $wait, reset: true);
        $this->save();
        $this->run();
    }

    private function next(int $wait = 0): void
    {
        if (empty($this->stack)) {
            $this->delete();
            return;
        }
        $this->wait(wait: $wait, reset: true);
        $this->save();
        $this->run();
    }

    private function wait(int $wait = 0, bool $reset = false): void
    {
        if ($reset) {
            $this->attempts = 0;
        }
        if ($this->attempts > 0) {
            $this->nextAttemptAt($wait);
            return;
        }
        $this->try_after = Carbon::now()->addMinutes($wait);
    }

    private function nextAttemptAt($wait = 0): void
    {
        if ($this->attempts <= 10) {
            $this->try_after = now()->addMinutes($this->attempts + $wait);
        }
        // exponentieel backoff na 10 tries, elke 1.5x langer wachten dan de vorige keer
        $this->try_after = now()->addMinutes((int)(10 * 1.5 ** ($this->attempts - 10)) + $wait);
    }

    private function filter_integer_recursive(array $array, int $target): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                // Als de waarde een array is, filteren we die eerst recursief
                $result[$key] = $this->filter_integer_recursive($value, $target);
            } elseif ($value !== $target) {
                // Als het een integer is en NIET de target, behouden we hem
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

    protected static function newFactory(): FlowFactory
    {
        return FlowFactory::new();
    }
}
