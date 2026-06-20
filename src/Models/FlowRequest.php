<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $request
 * @property Carbon $request_at
 * @property int $flow_id
 * @property Flow $flow
 */
class FlowRequest extends Model
{
    protected $table = 'flow_requests';

    protected $fillable=[
        'flow_id',
        'request',
        'request_at',
    ];

    protected function casts(): array
    {
        return [
             'request_at' => 'datetime',
        ];
    }
    public function flow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }
}
