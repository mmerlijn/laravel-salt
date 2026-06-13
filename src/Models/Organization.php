<?php
namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use mmerlijn\LaravelSalt\Http\Resources\Requester\OrganizationResource;
use mmerlijn\LaravelSalt\Models\Traits\CanHaveNotesTrait;
use mmerlijn\LaravelSalt\Models\Traits\PhoneModelTrait;
use mmerlijn\LaravelSalt\Observers\OrganizationObserver;
use mmerlijn\msgRepo\Enums\YesNoEnum;
use mmerlijn\msgRepo\HasAddressTrait;
use mmerlijn\msgRepo\Phone;

/**
 * @property string $initials
 * @property string $agbcode
 * @property string $vektis_name
 * @property mixed $building_nr
 * @property string $name
 * @property YesNoEnum $is_gp
 * @property array $owners
 * @property array $qualifications
 */

#[ObservedBy(OrganizationObserver::class), UseResource(OrganizationResource::class)]
class Organization extends Model
{
    use HasAddressTrait,CanHaveNotesTrait, PhoneModelTrait;
    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'agbcode',
        'building',
        'street',
        'city',
        'postcode',
        'country',
        'is_gp'
    ];
    protected function casts(): array
    {
        return [
            'is_gp' => YesNoEnum::class,
            'qualifications' => 'array',
            'owners' => 'array',
            'vektis_at' => 'datetime',
            'started_at' => 'datetime',
        ];
    }

    public function requesters(): BelongsToMany
    {
        return $this->belongsToMany(
            Requester::class,
            'organization_has_requester',
            'organization_agbcode',
            'requester_agbcode',
            'agbcode',
            'agbcode'
        )->withTimestamps();
    }
    public function scopeFilter($query, array $filter)
    {
        if (isset($filter['q'])) {
            $query->where('name', 'like', '%'.$filter['q'] . '%')
            ;
        }
        return $query;
    }
}