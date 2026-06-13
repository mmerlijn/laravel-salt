<?php


namespace mmerlijn\LaravelSalt\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use mmerlijn\msgRepo\Phone;


class PhoneCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return Phone
     */

    public function get(Model $model, string $key, mixed $value, array $attributes): \mmerlijn\msgRepo\Phone
    {
        return new \mmerlijn\msgRepo\Phone(number: $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (!$value instanceof \mmerlijn\msgRepo\Phone)
            $value = new \mmerlijn\msgRepo\Phone($value)->netNumber($attributes['city'] ?? "");
        return $value->number;
    }

}