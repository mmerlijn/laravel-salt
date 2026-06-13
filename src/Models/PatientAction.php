<?php

namespace mmerlijn\LaravelSalt\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use mmerlijn\LaravelSalt\Enums\PatientActionsEnum;

/**
 * @property int $id
 * @property PatientActionsEnum $type
 * @property array $comment
 * @property array $detail
 * @property array $actions
 * @property Carbon $at
 */
class PatientAction extends Model
{

    /**
     * @var array|mixed
     */

    protected $table = "patient_actions";

    protected $guarded=[];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'detail' => 'array',
            'type' => PatientActionsEnum::class,
        ];
    }
}
