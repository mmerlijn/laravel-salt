<?php

namespace mmerlijn\LaravelSalt\Models\Traits;


use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Models\Lock;

/**
 * @property MorphOne $lock
**/
/** @mixin Model */
trait CanBeLockedTrait
{
    public function lock(): MorphOne
    {
        return $this->morphOne(Lock::class, 'locked');
    }

    public function setLock(): void
    {
        if (auth()->user()) {
            $this->lock()->updateOrInsert([
                'locked_id' => $this->id,
                'locked_type' => __CLASS__,
            ],
                [
                    'lock_end' => Carbon::now()->addSeconds(90),
                    'user_id' => auth()->user()->id,
                ]);
            $this->refresh();
        }
    }

    protected function locked(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => $this->lock && $this->lock->user_id != auth()->user()?->id
        );
    }
}
