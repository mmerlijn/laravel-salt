<?php

namespace mmerlijn\LaravelSalt\Facades;


use Illuminate\Support\Facades\Facade;
use mmerlijn\LaravelSalt\Enums\ToastPositionEnum;
use mmerlijn\LaravelSalt\Enums\ToastTypeEnum;

/**
 * @method static flash(string $message = "", ToastTypeEnum $type = ToastTypeEnum::INFO, string $title = '', int $duration = 10000, ToastPositionEnum $position = ToastPositionEnum::TOP_RIGHT): self
 * @method static get(): array
 */
class Toast extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \mmerlijn\LaravelSalt\Helpers\Toast::class;
    }
}