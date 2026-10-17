<?php

namespace mmerlijn\LaravelSalt\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use mmerlijn\LaravelSalt\Models\Visible;

/**
 * @property MorphMany $visibles
 */

/** @mixin Model */
trait VisibleForTrait
{
    public function visibles(): MorphMany
    {
        return $this->morphMany(Visible::class, 'visibleable');
    }

    public function addVisibleFor(string $group): void
    {
        $this->visibles()->updateOrInsert([
            'visibleable_id' => $this->id,
            'visibleable_type' => $this->getMorphClass(),
        ],
            [
                'group' => $group
            ]);
        $this->refresh();
    }

    public function removeVisibleFor(string $group): void
    {
        $this->visibles()->where('group', $group)->delete();
        $this->refresh();
    }
}