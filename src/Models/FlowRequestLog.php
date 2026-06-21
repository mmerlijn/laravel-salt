<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class FlowRequestLog extends Model
{
    use MassPrunable;

    protected $table = 'flow_request_logs';

    protected $fillable = [
        'flow',
        'request',
        'request_at',
        'type',
        'patient_id',
        'request_nr',
    ];

    protected function casts(): array
    {
        return [
            'request_at' => 'datetime',
            'flow' => 'array',
        ];
    }

    public function prunable(): Builder
    {
        return static::query()->where('request_at', '<=', now()->minus(weeks: 1));
    }
}
