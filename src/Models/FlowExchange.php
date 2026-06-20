<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $request
 * @property string $response
 * @property Carbon $request_at
 * @property Carbon $response_at
 * @property int $flow_id
 * @property Flow $flow
 * @property mixed $type
 */
class FlowExchange extends Model
{
    protected $table = 'flow_exchanges';

    protected $fillable=[
        'flow_id',
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
        ];
    }
    public function flow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }
}
