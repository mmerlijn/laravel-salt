<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $request
 * @property string $response
 * @property Carbon $request_at
 * @property Carbon $response_at
 * @property mixed $type
 * @property int $patient_id
 * @property string $request_nr
 * @property array $flow
 */
class FlowLog extends Model
{
    use MassPrunable;

    protected $table = 'flow_logs';

    protected $fillable = [
        'flow',
        'request',
        'response',
        'request_at',
        'response_at',
        'type',
        'patient_id',
        'request_nr',
        'labtrain_id',
        'payload_id',
        'payload_type',
        'attempts',
    ];

    protected function casts(): array
    {
        return [
            'request_at' => 'datetime',
            'response_at' => 'datetime',
            'flow' => 'array',
        ];
    }

    public function prunable(): Builder
    {
        return static::query()->where('response_at', '<=', now()->minus(weeks: 1));
    }
}
