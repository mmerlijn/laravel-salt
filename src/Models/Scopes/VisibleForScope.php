<?php

namespace mmerlijn\LaravelSalt\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class VisibleForScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        if (!auth()->check()) {
            return;
        }
        if (auth()->user()->can('admin')) {
            return;
        }
        foreach (config('laravel_salt.visible_for_groups', []) as $group) {
            if (auth()->user()->can('visible_' . $group)) {
                $builder->has('visibles');
            }
        }

    }
}
