<?php

namespace mmerlijn\LaravelSalt\Helpers\Traits;

trait FormatTrait
{
    private static function reformatInput($data): array
    {
        foreach ($data as $k => $v) {
            if ($v === null) {
                unset($data[$k]);
            } elseif ($v === "") {
                $data[$k] = null;
            }
        }
        return $data;
    }
}
