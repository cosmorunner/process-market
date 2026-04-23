<?php

namespace App\Notifications;

use App\Mail\VerifyEmail as VerifyEmailMail;
use Illuminate\Notifications\Notification;

/**
 * Bestätigen der Registrierung.
 * Class VerifyEmail
 * @package App\Notifications
 */
class VerifyEmail extends Notification {

    /**
     * Get the notification's channels.
     * @param mixed $notifiable
     * @return array
     * @noinspection PhpUnusedParameterInspection
     */
    public function via($notifiable) {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     * @param mixed $notifiable
     * @return VerifyEmailMail
     */
    public function toMail($notifiable) {
        return (new VerifyEmailMail($notifiable));
    }

}
