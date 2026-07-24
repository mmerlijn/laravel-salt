<?php

namespace mmerlijn\LaravelSalt\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProblemNotification extends Notification
{
    use Queueable;

    public string $problem;

    public function __construct(string $problem)
    {
        $this->problem = $problem;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject(config('app.name') . ' - Er is een probleem')
            ->greeting('Beste beheerder')
            ->line('Er is een probleem met de agenda Salt applicatie.')
            ->line($this->problem)
            ->line('')
            ->salutation('Mvgr, Agenda Salt');

    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
