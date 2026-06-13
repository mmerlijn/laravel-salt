<?php

namespace mmerlijn\LaravelSalt\Enums;

enum ToastPositionEnum: string
{
    case TOP_LEFT = 'top-left';
    case TOP_RIGHT = 'top-right';

    case BOTTOM_LEFT = 'bottom-left';
    case BOTTOM_RIGHT = 'bottom-right';
    case TOP = "top-center";
    case BOTTOM = "bottom-center";

    public function label(): string
    {
        return match ($this) {
            ToastPositionEnum::TOP_LEFT => 'top-left',
            ToastPositionEnum::TOP_RIGHT => 'top-right',
            ToastPositionEnum::BOTTOM_LEFT => 'bottom-left',
            ToastPositionEnum::BOTTOM_RIGHT => 'bottom-right',
            ToastPositionEnum::TOP => 'top-center',
            ToastPositionEnum::BOTTOM => 'bottom-center',
        };
    }
}