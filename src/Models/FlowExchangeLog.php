<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowExchangeLog extends Model
{
    use MassPrunable;
    protected $table = 'flow_exchange_logs';

    protected $fillable= [
        'flow',
        'request',
        'response',
        'request_at',
        'response_at',
        'type',
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
