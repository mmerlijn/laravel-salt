<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $response
 * @property Carbon $response_at
 * @property int $flow_id
 * @property Flow $flow
 */
class FlowResponse extends Model
{
    protected $table = 'flow_responses';

    protected $fillable=[
        'flow_id',
        'response',
        'response_at',
    ];

    protected function casts(): array
    {
        return [
             'response_at' => 'datetime',
        ];
    }
    public function flow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }
}
