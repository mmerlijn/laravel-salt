<?php

namespace mmerlijn\LaravelSalt\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use DateTime;
use mmerlijn\LaravelSalt\Models\AppError;

class AppErrorNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function backoff(): int

    {
        return 20;
    }
    public function retryUntil(): DateTime
    {
        return Carbon::now()->addMinutes(2);
    }
    public function __construct(private readonly AppError $appError)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject(config('app.name').' application bug')
            ->view('laravel-salt::notifications.application-bug', ['error' => $this->appError])
            ->salutation("Met vriendelijke groet,\n".config('app.name'));
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
