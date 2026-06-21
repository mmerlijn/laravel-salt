<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class FlowResponseLog extends Model
{
    use MassPrunable;

    protected $table = 'flow_response_logs';

    protected $fillable = [
        'flow',
        'response',
        'response_at',
        'type',
        'port',
        'patient_id',
        'request_nr',
    ];

    protected function casts(): array
    {
        return [
            'response_at' => 'datetime',
            'flow' => 'array',
        ];
    }

    public function prunable(): Builder
    {
        return static::query()->where('response_at', '<=', now()->minus(weeks: 1));
    }
}
