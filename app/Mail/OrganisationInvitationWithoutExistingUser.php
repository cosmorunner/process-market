<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\Organisation;
use App\Models\Role;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Class OrganisationInvitationWithoutExistingUser
 * Einladung zu einer Organisation ohne dass der Benutzer bereits im System existiert.
 * @package App\Mail
 */
class OrganisationInvitationWithoutExistingUser extends Mailable {

    use Queueable, SerializesModels;

    public Invitation $invitation;
    public Organisation $organisation;
    public ?Role $role;
    public string $domain;
    public string $url;

    /**
     * Create a new message instance.
     * @param Invitation $invitation
     */
    public function __construct(Invitation $invitation) {
        $this->invitation = $invitation;
        $this->organisation = $invitation->resource;
        $this->role = $invitation->role;
        $this->domain = request()->getSchemeAndHttpHost();
        $this->url = route('register', ['invitation' => $this->invitation]);
    }

    /**
     * Build the message.
     * @return $this
     */
    public function build() {
        return $this->to($this->invitation->email)
            ->subject('Einladung zu der Organisation ' . $this->organisation->name)->view('emails.organisation-invitation-without-existing-user');
    }
}
