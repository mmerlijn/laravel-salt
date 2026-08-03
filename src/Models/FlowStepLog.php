<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;


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
 */
class FlowStepLog extends Model
{

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
        'active'
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

}
