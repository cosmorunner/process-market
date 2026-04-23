<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * Class InvitationCreated
 * Einladung zu einer Organisation ohne dass der Benutzer bereits im System existiert.
 * @package App\Mail
 */
class VerifyEmail extends Mailable {

    use Queueable, SerializesModels;

    public User $user;

    public string $url;

    /**
     * Create a new message instance.
     * @param User $user
     */
    public function __construct(User $user) {
        $this->user = $user;
        $this->url = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build() {
        return $this->to($this->user->email)
            ->subject('E-Mail Bestätigung')->view('emails.verify-email');
    }
}
