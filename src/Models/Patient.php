<?php

namespace mmerlijn\LaravelSalt\Models;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\UseResource;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use mmerlijn\LaravelSalt\Actions\FindOrCreatePatient;
use mmerlijn\LaravelSalt\Casts\PhoneCast;
use mmerlijn\LaravelSalt\Http\Resources\Patient\PatientResource;
use mmerlijn\LaravelSalt\Models\Traits\AccessLogsTrait;
use mmerlijn\LaravelSalt\Models\Traits\AddressModelTrait;
use mmerlijn\LaravelSalt\Models\Traits\CanBeLockedTrait;
use mmerlijn\LaravelSalt\Models\Traits\CanHaveAppointmentTrait;
use mmerlijn\LaravelSalt\Models\Traits\CanHaveNotesTrait;
use mmerlijn\LaravelSalt\Models\Traits\FlowModelTrait;
use mmerlijn\LaravelSalt\Models\Traits\NameModelTrait;
use mmerlijn\LaravelSalt\Observers\PatientObserver;
use mmerlijn\LaravelSalt\Rules\Bsn;
use mmerlijn\msgRepo\Address;
use mmerlijn\msgRepo\Enums\LangEnum;
use mmerlijn\msgRepo\Enums\PatientSexEnum;
use mmerlijn\msgRepo\Id;
use mmerlijn\msgRepo\Insurance;
use mmerlijn\msgRepo\Name;
use mmerlijn\msgRepo\Patient as PatientRepo;
use mmerlijn\msgRepo\Phone;
use Workbench\Database\Factories\PatientFactory;

/**
 * @property int $id
 * @property string $email
 * @property PatientSexEnum $sex
 * @property string $initials
 * @property string $lastname
 * @property string $own_lastname
 * @property string $prefix
 * @property string $own_prefix
 * @property Carbon $dob
 * @property Carbon $deceased
 * @property string $bsn
 * @property string $postcode
 * @property string $city
 * @property string $street
 * @property string $building
 * @property string $country
 * @property string $last_requester
 * @property string $last_organization
 * @property Phone $phone
 * @property Phone $phone2
 * @property string $uzovi
 * @property string $policy_nr
 * @property string $lbsnr
 * @property array $labels
 * @property int $created_by
 * @property Address $address
 * @property Name $name
 * @property PatientAction $action
 * @property Requester $requester
 * @property Carbon $email_verified_at
 * @property LangEnum $lang
 * @property string $general_practitioner
 * @property Requester $gp
 * @property Requester $organization
 * @property string $phone_note
 * @property string $email_ext
 * @property int $contact_id
 * @property int $labtrain_id
 */
#[ObservedBy(PatientObserver::class), UseResource(PatientResource::class)]
class Patient extends Model
{
    use Notifiable, HasFactory, SoftDeletes, NameModelTrait, AddressModelTrait,
        AccessLogsTrait, CanHaveAppointmentTrait, CanHaveNotesTrait, CanBeLockedTrait, FlowModelTrait;


    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'dob' => 'date:Y-m-d',
            'labels' => 'array',
            'phone' => PhoneCast::class,
            'phone2' => PhoneCast::class,
            'sex' => PatientSexEnum::class,
            'email_verified_at' => 'datetime',
            'lang' => LangEnum::class,
            'deceased' => 'datetime',
        ];
    }

    /*
     * Relations
     * */
    public function action(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PatientAction::class, 'id', 'id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(PatientAction::class);
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(AccessLog::class);
    }

    public function followups(): HasMany
    {
        $class = config('laravel-salt.classes.followup');

        if (!$class) {
            throw new \RuntimeException(
                "De 'laravel-salt.classes.followup' configuratie is niet ingesteld, maar de followup relatie wordt wel aangeroepen."
            );
        }
        return $this->hasMany($class::class);;

    }

    public function appointments(): MorphMany
    {
        $class = config('laravel-salt.classes.appointment');
        if (!$class) {
            throw new \RuntimeException(
                "De 'laravel-salt.classes.appointment' configuratie is niet ingesteld, maar de appointments relatie wordt wel aangeroepen."
            );
        }
        return $this->morphMany($class, 'owner');
    }

    public function tests(): HasMany
    {
        $class = config('laravel-salt.classes.test');
        if (!$class) {
            throw new \RuntimeException(
                "De 'laravel-salt.classes.test' configuratie is niet ingesteld, maar de tests relatie wordt wel aangeroepen."
            );
        }
        return $this->hasMany($class::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Requester::class, 'last_requester', 'agbcode')->withTrashed()->withDefault([
            'initials' => '',
            'own_lastname' => 'Niet bekend',
            'agbcode' => '00000000',
        ]);
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Requester::class, 'last_requester', 'agbcode')->withTrashed()->withDefault([
            'name' => 'Niet bekend',
            'agbcode' => '00000000',
        ]);

    }

    public function gp(): BelongsTo
    {
        return $this->belongsTo(Requester::class, 'general_practitioner', 'agbcode')->withTrashed()->withDefault([
            'name' => 'Niet bekend',
            'agbcode' => '00000000',
        ]);
    }

    public function requests(): HasMany
    {
        $class = config('laravel-salt.classes.request');
        if (!$class) {
            throw new \RuntimeException("De 'laravel-salt.classes.request' configuratie is niet ingesteld, maar de request relatie wordt wel aangeroepen.");
        }
        return $this->hasMany($class::class);
    }

    /* TODO tzt uitbreiden met patient_id om als match te gebruiken */
    public static function getPatient(\mmerlijn\msgRepo\Patient $patient, bool $create = false): ?Patient
    {
        try {
            if ($patient->getLabtrainId()) {
                $p = self::whereLabtrainId($patient->getLabtrainId())->first();
                if ($p) {
                    return $p;
                }
            }
            $v = Validator::make(['bsn' => $patient->bsn], [
                'bsn' => [new Bsn],
            ])->validate();;
            //valide bsn
            $p = self::whereBsn($patient->bsn)->first();
            if ($p->dob == $patient->dob) {
                return $p;
            }
            if ($p->own_lastname == $patient->name->own_lastname) {
                return $p;
            }
        } catch (\Exception $e) {
            //geen valide bsn
        }
        foreach (self::whereDob($patient->dob)->where('own_lastname', $patient->name->own_lastname)->wherePostcode($patient->address->postcode)->get() as $p) {
            if (strtolower(str_replace(".", "", $p->initials)) == strtolower(str_replace(".", "", $p->initials))) {
                return $p;
            }
        }
        if ($create) {
            try {
                return new FindOrCreatePatient()($patient);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }
    /**
     * Query helpers
     */

    /**
     * @param $query
     * @param array $filter
     * @return mixed
     */
    public function scopeFiltered($query, array $filter)
    {
        $query = $query->useIndex('patient_search_index');
        if ($filter['patient_id'] ?? false) {
            $query = $query->whereId($filter['patient_id']);
        }
        if ($filter['sex'] ?? false) {
            $query = $query->whereSex($filter['sex']);
        }
        if ($filter['requester'] ?? false) {
            $query = $query->whereLastRequester($filter['requester']);
        }
        if ($filter['name'] ?? false) {
            $query = $query->where(fn($q) => $q->where('lastname', 'like', $filter['name'] . "%")->orWhere('own_lastname', 'like', $filter['name'] . '%'));
        }
        if ($filter['city'] ?? false) {
            $query = $query->whereCity($filter['city']);
        }
        if ($filter['postcode'] ?? false) {
            $query = $query->wherePostcode($filter['postcode']);
        }
        if ($filter['email'] ?? false) {
            $query = $query->whereEmail($filter['email']);
        }
        if ($filter['dob'] ?? false) {
            try {
                $date = Carbon::parse($filter['dob']);
            } catch (InvalidFormatException $e) {
                $date = false;
            }
            if ($date) {
                $query = $query->whereDate('dob', $date);
            }
        }
        if ($filter['bsn'] ?? false) {
            try {
                $validator = Validator::make($filter, ['bsn' => [new Bsn]]);
                if ($validator->validate()) {
                    $query = $query->whereBsn($filter['bsn']);
                }
            } catch (\Exception $e) {
            }


        }
        return $query;
    }

    public function age(Carbon|null $date = null): float
    {
        if (!$date) {
            $date = now();
        }
        return $this->dob->diffInYears($date);
    }

    protected function patient(): Attribute
    {
        return new Attribute(
            get: function ($value, $attributes) {
                $p = new PatientRepo(
                    name: $this->name,
                    address: $this->address,
                    last_requester: $attributes['last_requester'] ?? ''
                )->setDob($attributes['dob'])
                    ->setBsn($attributes['bsn'] ?? "")
                    ->setSex($attributes['sex'])
                    ->setInsurance($this->insurance)
                    ->addPhone($attributes['phone'] ?? "")
                    ->addPhone($attributes['phone2'] ?? "")
                    ->setEmail($attributes['email'] ?: ($attributes['email_ext'] ?? ""));
                if ($attributes['labtrain_id']) {
                    $p->addId(new Id(id: $attributes['labtrain_id'], authority: "SALT", code: "VN"));
                }
                return $p;
            },

            set: fn(PatientRepo $patient) => [
                'sex' => $patient->sex->value,
                'bsn' => $patient->bsn,
                'dob' => $patient->dob,
                'initials' => $patient->name->initials,
                'lastname' => $patient->name->lastname,
                'prefix' => $patient->name->prefix,
                'own_lastname' => $patient->name->own_lastname,
                'own_prefix' => $patient->name->own_prefix,
                'city' => $patient->address->city,
                'street' => $patient->address->street,
                'postcode' => $patient->address->postcode,
                'building' => $patient->address->building,
                'uzovi' => $patient->insurance->uzovi,
                'policy_nr' => $patient->insurance->policy_nr,
                'last_requester' => $patient->last_requester,
            ],
        );
    }

    protected function insurance(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => ($u = Uzovi::find($attributes['uzovi'])) ?
                $u->insurance->addPolicyNr($attributes['policy_nr'] ?? "") :
                new Insurance(policy_nr: $attributes['policy_nr'] ?? ""),
        );
    }

    public function routeNotificationForMail($notification): ?string
    {
        if ($this->email) {
            return $this->email;
        }
        return null;
    }


    public function routeNotificationForVonage($notification): ?string
    {
        if ($this->phone->canReceiveSms()) {
            return $this->phone->forSms();
        }
        return null;
    }

    protected static function newFactory(): PatientFactory
    {
        return PatientFactory::new();
    }

}
