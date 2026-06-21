<?php

namespace mmerlijn\LaravelSalt\Models\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use mmerlijn\LaravelSalt\Models\Flow;

trait FlowModelTrait
{
    public function flows(): MorphMany
    {
        return $this->morphMany(Flow::class, 'payload');
    }
}