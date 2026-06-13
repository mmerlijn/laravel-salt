<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $user_id
 * @property int $locked_id
 * @property Carbon $lock_end
 */
class Lock extends Model
{
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'lock_end' => 'datetime'
        ];
    }
    public function extend($sec = 90): void
    {
        $this->lock_end = Carbon::now()->addSeconds($sec);
        $this->user_id = auth()->user()?->id ?: 500;
        $this->save();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

}
