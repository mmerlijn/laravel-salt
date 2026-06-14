<?php

namespace mmerlijn\LaravelSalt\Models;


use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use mmerlijn\LaravelSalt\Http\Resources\Requester\RequesterResource;
use mmerlijn\LaravelSalt\Jobs\GetCaregiverJob;
use mmerlijn\LaravelSalt\Models\Traits\CanHaveNotesTrait;
use mmerlijn\LaravelSalt\Models\Traits\NameModelTrait;
use mmerlijn\LaravelSalt\Observers\RequesterObserver;
use mmerlijn\msgRepo\Contact;
use mmerlijn\msgRepo\Enums\VektisType;
use mmerlijn\msgRepo\Enums\YesNoEnum;
use mmerlijn\msgRepo\HasNameTrait;
use mmerlijn\msgRepo\Name;


/**
 * @property string $agbcode
 * @property Name $name
 * @property YesNoEnum $is_gp
 */
#[ObservedBy(RequesterObserver::class), UseResource(RequesterResource::class)]
class Requester extends Model
{
    use SoftDeletes, CanHaveNotesTrait, HasNameTrait, NameModelTrait;

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
    ];

    public static function getRequesterByAgbcode(string $agbcode):?Requester
    {
        return Requester::withTrashed()->find(trim($agbcode));

    }

    public static function add(Contact $requester, VektisType $type = VektisType::ZORGVERLENER): ?Requester
    {
        if (!$requester->agbcode) {
            return null;
        }
        if($r = self::getRequesterByAgbcode($requester->agbcode)){
            return $r;
        }
        logger($requester->agbcode);
        logger(Requester::withTrashed()->find($requester->agbcode));
        $r_new = new self;
        $r_new->agbcode = $requester->agbcode;
        if ($requester instanceof Contact) {
            $r_new->name = $requester->name;
        }
        $r_new->save();

        if(config('laravel_salt.vektis',false)){
            GetCaregiverJob::dispatch($requester->agbcode,VektisType::ZORGVERLENER);
        }
        return $r_new;
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(
            Organization::class,
            'organization_has_requester',
            'requester_agbcode',
            'organization_agbcode',
            'agbcode',
            'agbcode'
        )->withTimestamps();
    }

    //use for RequesterAPI
    public function scopeFilter($query, array $filter)
    {
        if (isset($filter['q'])) {
            $query->where('own_lastname', 'like', $filter['q'] . '%')
            ;
        }
        return $query;
    }
}
