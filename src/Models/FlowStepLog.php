<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Http\Resources\FlowStepLogResource;


/**
 * @property null|FlowError $error
 * @property array $stack
 * @property Carbon $try_after
 * @property int $attempts
 * @property int $flow_error_id
 * @property int $payload_id
 * @property string $payload_type
 * @property string $origin
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
 * @property int $task
 * @property int $flow_id
 * @property int $id
 */
#[UseResource(FlowStepLogResource::class)]
class FlowStepLog extends Model
{
    use MassPrunable;

    protected $fillable = [
        'type',
        'stack',
        'origin',
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
        'active',
        'task',
        'flow_id',
    ];
    protected $table = 'flow_step_logs';

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


    public function payload(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    /**
     * Define the prunable query for the model.
     */
    public function prunable(): Builder
    {
        return static::where('created_at', '<=', Carbon::now()->subWeek());
    }
}
