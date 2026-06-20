<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowResponseLog extends Model
{
    use MassPrunable;

    protected $table = 'flow_response_logs';

    protected $fillable=[
        'flow',
        'response',
        'response_at',
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
