<?php

namespace mmerlijn\LaravelSalt\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $from_agbcode
 * @property string $to_agbcode
 * @property Requester $to
 * @property Requester $from
 * @property Carbon $from_date
 * @property string $name
 */
class RequesterConvertor extends Model
{
    use HasFactory;

    protected $table = 'requester_convertors';

    protected $guarded = [];


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'from_date' => 'date',
        ];
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(Requester::class, 'from_agbcode', 'agbcode')->withTrashed();
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Requester::class, 'to_agbcode', 'agbcode')->withTrashed();
    }

    public static function requester(string $agbcode): ?Requester
    {
        $convertor = self::where('from_agbcode', $agbcode)->where('from_date', '<=', Carbon::now())->first();
        if (!$convertor) {
            return Requester::withTrashed()->where('agbcode', $agbcode)->first();
        }
        return $convertor?->to;
    }

    public static function convertAgbcode(?string $agbcode_hp, ?string $agbcode_requester): string
    {
        if ($agbcode_requester == $agbcode_hp) {
            return $agbcode_requester; // No conversion needed if they are the same
        }
        if ($agbcode_hp) {
            $aanvrager = self::where('from_agbcode', $agbcode_hp)->first();
            if ($aanvrager) {
                return $aanvrager->to_agbcode;
            }
        }
        $aanvrager = self::where('from_agbcode', $agbcode_requester)->first();
        if ($aanvrager) {
            return $aanvrager->to_agbcode;
        }
        return $agbcode_requester; // Return original if no conversion found
    }

}
