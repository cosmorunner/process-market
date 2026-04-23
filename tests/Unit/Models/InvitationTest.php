<?php

namespace Tests\Unit\Models;

use App\Models\Invitation;
use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class InvitationTest
 * @package Tests\Unit\Models
 */
class InvitationTest extends TestCase {

    use RefreshDatabase;

    public function test_invitation_has_an_id() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->create();
        $this->assertIsString($invitation->id);
    }

    public function test_invitation_has_an_email() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->create();
        $this->assertIsString($invitation->email);
    }

    public function test_invitation_has_a_resource_id() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->create();
        $this->assertIsString($invitation->resource_id);
    }

    public function test_invitation_has_a_resource_type() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->create();
        $this->assertEquals(Organisation::class, $invitation->resource_type);
    }

    public function test_invitation_has_a_role_id() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->create();
        $this->assertIsString($invitation->role_id);
    }

    public function test_invitation_has_a_creator_id() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->create();
        $this->assertIsString($invitation->creator_id);
    }

    public function test_invitation_has_an_expires_at() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->create();
        $this->assertNotNull($invitation->expires_at);
    }

    public function test_invitation_has_a_resource() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->ofResource()->create();
        $this->assertInstanceOf(Organisation::class, $invitation->resource);
    }

    public function test_invitation_has_a_role() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->ofRole()->create();
        $this->assertInstanceOf(Role::class, $invitation->role);
    }

    public function test_invitation_has_a_creator() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->ofCreator()->create();
        $this->assertInstanceOf(User::class, $invitation->creator);
    }

    public function test_invitation_returns_all_system_invitations() {
        /* @var Invitation $invitation */
        Invitation::factory()->create();
        $systemInvitation = Invitation::factory()->system()->create();

        $this->assertCount(1, Invitation::systemInvitations());
        $this->assertEquals($systemInvitation->id, Invitation::systemInvitations()->first()->id);
    }

    public function test_invitation_deletes_expired_invitations() {
        /* @var Invitation $invitation */
        $validInvitation = Invitation::factory()->create();
        Invitation::factory()->expired()->create();

        $this->assertDatabaseCount('invitations', 2);

        Invitation::deleteExpiredInvitations();
        $this->assertDatabaseCount('invitations', 1);
        $this->assertDatabaseHas('invitations', ['id' => $validInvitation->id]);
    }

    public function test_invitation_an_not_expired_invitation_is_still_valid() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->create();
        $this->assertTrue($invitation->isValid());
    }

    public function test_invitation_an_expired_invitation_is_not_valid_anymore() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->expired()->create();
        $this->assertTrue($invitation->isInvalid());
    }

    public function test_invitation_renew_expired_invitation() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->expired()->create();
        $this->assertFalse($invitation->isValid());
        $invitation->renewExpiry();
        $this->assertTrue($invitation->isValid());
    }

    public function test_invitation_a_system_invitation_is_only_valid_when_email_does_not_exist_yet() {
        /* @var Invitation $invitation */
        $invitation = Invitation::factory()->system()->create();
        $this->assertTrue($invitation->isValid());

        // Benutzer mit der E-Mail-Adresse der Einladung erzeugen.
        User::factory()->create(['email' => $invitation->email]);
        $this->assertFalse($invitation->isValid());
    }

    public function test_invitation_for_an_organisation_is_valid_when_email_exists_or_does_not_exist() {
        /* @var Invitation $invitation */
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withDefaultRoles()->create();
        $invitation = Invitation::factory()->ofResource($organisation)->ofRole($organisation->roles->first())->create();
        $this->assertTrue($invitation->isValid());
        User::factory()->create(['email' => $invitation->email]);
        $this->assertTrue($invitation->isValid());
    }

    public function test_invitation_an_organisation_invitation_is_only_valid_when_organisation_still_exists() {
        /* @var Invitation $invitation */
        /* @var Organisation $organisation */
        $organisation = Organisation::factory()->withDefaultRoles()->create();
        $invitation = Invitation::factory()->ofResource($organisation)->ofRole($organisation->roles->first())->create();
        $this->assertTrue($invitation->isValid());

        $organisation->delete();
        $invitation->refresh();

        // Organisation existiert nicht mehr.
        $this->assertFalse($invitation->isValid());
    }
}
