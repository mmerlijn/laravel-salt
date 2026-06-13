<?php

namespace mmerlijn\LaravelSalt\Models;


use Illuminate\Support\Carbon;
use mmerlijn\LaravelSalt\Http\Resources\Tool\UzoviResource;
use mmerlijn\LaravelSalt\Models\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use mmerlijn\msgRepo\Insurance;


/**
 * @property string $note
 * @property string $website
 * @property string $code
 * @property string $name
 * @property Carbon $active_from
 * @property Carbon $active_to
 */
#[ScopedBy(ActiveScope::class), UseResource(UzoviResource::class)]
class Uzovi extends Model
{
    protected $table = "tool_uzovi";
    public $incrementing = false;
    protected $primaryKey = 'code';
    protected $casts = [
        'active_from' => 'datetime',
        'active_to' => 'datetime',
    ];

    #[Scope]
    protected function name(Builder $query, string $name): void
    {
        $query->where(fn($q) => $q->where('name', 'like', '%' . $name . '%')
            ->orWhere('concern', 'like', $name . '%')
        );
    }

    #[Scope]
    protected function active(Builder $query, bool $active = true): void
    {
        if (!$active) {
            $query->withoutGlobalScope(ActiveScope::class);
        }
    }

    protected function insurance(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => new Insurance(uzovi: $attributes['code'], company_name: $attributes['name']),
        );
    }
}
