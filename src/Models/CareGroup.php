<?php

namespace mmerlijn\LaravelSalt\Models;


use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use mmerlijn\LaravelSalt\Enums\CareGroupEnum;
use mmerlijn\LaravelSalt\Enums\TestTypeEnum;
use mmerlijn\LaravelSalt\Http\Resources\CareGroupResource;
use Workbench\Database\Factories\CareGroupFactory;

#[UseResource(CareGroupResource::class)]
class CareGroup extends Model
{
    use HasFactory;

    protected $table = 'care_group';
    protected $guarded = [];

    protected array $cast = [
        'care_group' => CareGroupEnum::class,
        'test_type' => TestTypeEnum::class,
    ];


    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(\mmerlijn\LaravelSalt\Models\Requester::class, 'agbcode', 'agbcode')->withTrashed();
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(\mmerlijn\LaravelSalt\Models\Requester::class, 'agbcode', 'agbcode')->withTrashed();
    }

    protected static function newFactory(): CareGroupFactory|Factory
    {
        return CareGroupFactory::new();
    }

    public static function getCareGroup(\mmerlijn\LaravelSalt\Models\Requester|string|null $requester, \mmerlijn\LaravelSalt\Models\Requester|string|null $organization, ?TestTypeEnum $testType = null): CareGroupEnum
    {
        if (gettype($requester) === 'string') {
            $requester = \mmerlijn\LaravelSalt\Models\Requester::where('agbcode', $requester)->first();
        }
        if (gettype($organization) === 'string') {
            $organization = \mmerlijn\LaravelSalt\Models\Requester::where('agbcode', $organization)->first();
        }
        //SEZ relation: 53530008
        //SAG relation: 53530042
        //honk relation: 53530068
        //ROHA relation: 53530328
        //Arts en Zorg:  	17081547 of 53530217
        if ($testType === null) {
            return CareGroupEnum::_;
        }
        $careGroup = CareGroup::where(fn($q) => $q->whereAgbcode($requester?->agbcode)->orWhere('agbcode', $organization?->agbcode))
            ->where('test_type', $testType)
            ->first();
        if ($careGroup) {
            return CareGroupEnum::from($careGroup->care_group);
        }
        return CareGroupEnum::_;

    }

    //use for RequesterAPI
    public function scopeFilter($query, array $filter)
    {
        if ($filter['test_type'] ?? false) {
            $query->where('test_type', $filter['test_type']);
        }
        if ($filter['agbcode'] ?? false) {
            $query->where('agbcode', $filter['agbcode']);
        }
        if ($filter['care_group'] ?? false) {
            $query->where('care_group', $filter['care_group']);
        }
        if ($filter['requester_type'] ?? false) {
            $query->whereHas('requester', fn($q) => $q->where('type', $filter['requester_type'])
            );
        }
        if ($filter['requester_name'] ?? false) {
            $query->whereHas('requester', fn($q) => $q->where('vektis_name', 'like', '%' . ($filter['requester_name'] ?? "") . "%")
            );
        }
        if ($filter['requester_postcode'] ?? false) {
            $query->whereHas('requester', fn($q) => $q->where('postcode', $filter['requester_postcode'] ?? "")
            );
        }
        return $query;
    }
}
