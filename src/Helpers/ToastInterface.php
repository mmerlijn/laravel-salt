<?php

namespace mmerlijn\LaravelSalt\Helpers;



use mmerlijn\LaravelSalt\Enums\ToastPositionEnum;
use mmerlijn\LaravelSalt\Enums\ToastTypeEnum;

interface ToastInterface
{
    public function flash(string $message = "", ToastTypeEnum $type = ToastTypeEnum::INFO, string $title = '', int $duration = 10000, ToastPositionEnum $position = ToastPositionEnum::TOP_RIGHT): self;

    public function get(): array;
}