<?php

namespace mmerlijn\LaravelSalt\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class ActiveScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * Database fields: 'active_from', 'active_to' have to be present in the model
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where(fn($q) => $q->whereNull('active_from')->orWhereDate('active_from', '<=', now()))
            ->where(fn($q) => $q->whereNull('active_to')->orWhereDate('active_to', '>=', now()));
    }
}
