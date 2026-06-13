<?php

namespace mmerlijn\LaravelSalt\Helpers;


use mmerlijn\LaravelSalt\Enums\ToastPositionEnum;
use mmerlijn\LaravelSalt\Enums\ToastTypeEnum;

class Toast implements ToastInterface
{
    public function __construct()
    {
    }

    public function flash(string $message = "", ToastTypeEnum $type = ToastTypeEnum::INFO, string $title = '', int $duration = 5000, ToastPositionEnum $position = ToastPositionEnum::TOP_RIGHT): self
    {
        $toasts = session('toasts', []);
        $toasts[] = [
            'message' => $message,
            'duration' => $duration,
            'type' => $type,
            'position' => $position,
            'title' => '',
        ];
        session()->flash('toasts', $toasts);
        return $this;
    }

    public function get(): array
    {
        $flash = session('toasts', []);
        if (session()->has('success')) {
            $success = session('success');
            if (gettype($success) == 'array') {
                $flash[] = [...$success, 'type' => 'success'];
            } elseif (gettype($success) == 'string') {
                $flash[] = [
                    'type' => 'success',
                    'message' => $success
                ];
            }
        }
        if (session()->has('error')) {
            $error = session('error');
            if (gettype($error) == 'array') {
                $flash[] = [...$error, 'type' => 'error'];
            } elseif (gettype($error) == 'string') {
                $flash[] = [
                    'type' => 'error',
                    'message' => $error
                ];
            }
        }
        if (session()->has('errors')) {
            $duration = 3000;
            foreach (session('errors')->toArray() as $errors) {
                foreach ($errors as $error) {
                    $flash[] = [
                        'type' => 'error',
                        'message' => $error,
                        'duration' => $duration,
                        'title' => 'Validatie error'
                    ];
                    $duration += 3000;
                }
            }
        }
        if (session()->has('warning')) {
            $warning = session('warning');
            if (gettype($warning) == 'array') {
                $flash[] = [...$warning, 'type' => 'warning'];
            } elseif (gettype($warning) == 'string') {
                $flash[] = [
                    'type' => 'warning',
                    'message' => $warning
                ];
            }
        }
        return $flash;
    }
}