<?php

namespace mmerlijn\LaravelSalt\Models;


use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use mmerlijn\LaravelSalt\Http\Resources\Requester\RequesterResource;
use mmerlijn\LaravelSalt\Models\Traits\AddressModelTrait;
use mmerlijn\LaravelSalt\Models\Traits\CanHaveNotesTrait;
use mmerlijn\LaravelSalt\Models\Traits\FlowModelTrait;
use mmerlijn\LaravelSalt\Models\Traits\NameModelTrait;
use mmerlijn\LaravelSalt\Observers\RequesterObserver;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Enums\PatientSexEnum;
use mmerlijn\msgRepo\Enums\VektisType;
use mmerlijn\msgRepo\Enums\YesNoEnum;
use mmerlijn\msgRepo\HasNameTrait;
use mmerlijn\msgRepo\Phone;
use Workbench\Database\Factories\RequesterFactory;


/**
 * @property string $agbcode
 * @property YesNoEnum $is_gp
 * @property string $initials
 * @property mixed $building_nr
 * @property string $name
 * @property Address $address
 * @property Phone $phone
 * @property array $owners
 * @property array $qualifications
 */
#[ObservedBy(RequesterObserver::class), UseResource(RequesterResource::class)]
class Requester extends Model
{
    use HasFactory, SoftDeletes, CanHaveNotesTrait, HasNameTrait, NameModelTrait, AddressModelTrait, FlowModelTrait;

    protected $primaryKey = 'agbcode';
    public $incrementing = false;
    protected $table = 'requesters';

    protected $guarded = [];

    protected $casts = [
        'is_gp' => YesNoEnum::class,
        'qualifications' => 'array',
        'owners' => 'array',
        'vektis_at' => 'datetime',
        'started_at' => 'datetime',
        'type' => VektisType::class,
        'sex' => PatientSexEnum::class,
    ];

    public static function getRequesterByAgbcode(string $agbcode): ?Requester
    {
        return Requester::withTrashed()->whereAgbcode(trim($agbcode))->first();

    }

    // De kant waarbij dit model de zorgverlener/aanvrager is
    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(
            Requester::class,
            'organization_has_requester',
            'requester_agbcode',
            'organization_agbcode'
        )->withTimestamps();
    }

    // De kant waarbij dit model de organisatie is
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(
            Requester::class,
            'organization_has_requester',
            'organization_agbcode',
            'requester_agbcode'
        )->withTimestamps();
    }

    public function getRelatedAttribute(): BelongsToMany
    {

        if ($this->type == VektisType::ZORGVERLENER) {
            return $this->organizations;
        } else {
            return $this->members;
        }
    }

    //use for RequesterAPI
    public function scopeFilter($query, array $filter)
    {
        if (isset($filter['q'])) {
            $query->whereAny([
                'vektis_name',
                'own_lastname',
            ], 'like', '%' . $filter['q'] . '%');

        }
        if (isset($filter['type'])) {
            $query->whereType($filter['type']);
        }
        return $query;
    }

    protected static function newFactory(): RequesterFactory
    {
        return RequesterFactory::new();
    }
}
