<?php

namespace mmerlijn\LaravelSalt\Enums;

enum ToastTypeEnum: string
{
    case SUCCESS = 'success';
    case ERROR = "error";
    case WARNING = "warning";
    case INFO = "info";
    case ACTION = "action";

    public function label(): string
    {
        return match ($this) {
            ToastTypeEnum::SUCCESS => 'success',
            ToastTypeEnum::ERROR => 'error',
            ToastTypeEnum::WARNING => 'warning',
            ToastTypeEnum::INFO => 'info',
            ToastTypeEnum::ACTION => 'action',
        };
    }
}