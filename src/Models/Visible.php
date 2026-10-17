<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use mmerlijn\LaravelSalt\Enums\LockTypeEnum;

/**
 * @property int $visibleable_id
 * @property LockTypeEnum $visibleable_type
 * @property string $group
 */
class Visible extends Model
{
    protected $table = 'visibles';

    protected function casts(): array
    {
        return [
            'visibleable_type' => LockTypeEnum::class,
        ];
    }

    public function visibleable(): MorphTo
    {
        return $this->morphTo();
    }
}
