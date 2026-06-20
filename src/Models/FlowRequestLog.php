<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowRequestLog extends Model
{
    use MassPrunable;
    protected $table = 'flow_request_logs';

    protected $fillable=[
        'flow',
        'request',
        'request_at',
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
