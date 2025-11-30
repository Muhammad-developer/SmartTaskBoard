<?php

namespace Tests\Feature;

use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TeamTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_authenticated_user_can_create_team(): void
    {
        $teamData = [
            'name' => 'Development Team',
            'description' => 'Team for development projects',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/teams', $teamData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'description',
                'owner_id',
                'created_at',
                'updated_at',
            ])
            ->assertJson([
                'name' => 'Development Team',
                'description' => 'Team for development projects',
                'owner_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'Development Team',
            'owner_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $response->json('id'),
            'user_id' => $this->user->id,
            'role' => 'owner',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_team(): void
    {
        $teamData = [
            'name' => 'Development Team',
        ];

        $response = $this->postJson('/api/teams', $teamData);

        $response->assertStatus(401);
    }

    public function test_team_creation_requires_name(): void
    {
        $teamData = [
            'description' => 'Team description',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/teams', $teamData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_team_owner_can_invite_member(): void
    {
        Mail::fake();

        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);

        $inviteData = [
            'email' => 'newmember@example.com',
            'role' => 'member',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/teams/{$team->id}/invite", $inviteData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Invitation sent successfully',
            ]);

        $this->assertDatabaseHas('team_invitations', [
            'team_id' => $team->id,
            'email' => 'newmember@example.com',
            'role' => 'member',
        ]);
    }

    public function test_team_member_cannot_invite_new_members(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $team->members()->attach($this->user->id, ['role' => 'member']);

        $inviteData = [
            'email' => 'newmember@example.com',
            'role' => 'member',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/teams/{$team->id}/invite", $inviteData);

        $response->assertStatus(403);
    }

    public function test_team_admin_can_invite_members(): void
    {
        Mail::fake();

        $team = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $team->members()->attach($this->user->id, ['role' => 'admin']);

        $inviteData = [
            'email' => 'newmember@example.com',
            'role' => 'member',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/teams/{$team->id}/invite", $inviteData);

        $response->assertStatus(200);
    }

    public function test_cannot_invite_existing_team_member(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);
        $team->members()->attach($this->otherUser->id, ['role' => 'member']);

        $inviteData = [
            'email' => $this->otherUser->email,
            'role' => 'member',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/teams/{$team->id}/invite", $inviteData);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'User is already a team member',
            ]);
    }

    public function test_team_owner_can_remove_member(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);
        $team->members()->attach($this->otherUser->id, ['role' => 'member']);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/teams/{$team->id}/members/{$this->otherUser->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Member removed successfully',
            ]);

        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $this->otherUser->id,
        ]);
    }

    public function test_team_member_cannot_remove_other_members(): void
    {
        $thirdUser = User::factory()->create();

        $team = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $team->members()->attach($this->user->id, ['role' => 'member']);
        $team->members()->attach($thirdUser->id, ['role' => 'member']);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/teams/{$team->id}/members/{$thirdUser->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $thirdUser->id,
        ]);
    }

    public function test_team_owner_cannot_remove_themselves(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/teams/{$team->id}/members/{$this->user->id}");

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Team owner cannot be removed',
            ]);
    }

    public function test_team_owner_can_change_member_role(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);
        $team->members()->attach($this->otherUser->id, ['role' => 'member']);

        $response = $this->actingAs($this->user)
            ->putJson("/api/teams/{$team->id}/members/{$this->otherUser->id}", [
                'role' => 'admin',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Member role updated successfully',
            ]);

        $this->assertDatabaseHas('team_user', [
            'team_id' => $team->id,
            'user_id' => $this->otherUser->id,
            'role' => 'admin',
        ]);
    }

    public function test_team_member_cannot_change_roles(): void
    {
        $thirdUser = User::factory()->create();

        $team = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $team->members()->attach($this->user->id, ['role' => 'member']);
        $team->members()->attach($thirdUser->id, ['role' => 'member']);

        $response = $this->actingAs($this->user)
            ->putJson("/api/teams/{$team->id}/members/{$thirdUser->id}", [
                'role' => 'admin',
            ]);

        $response->assertStatus(403);
    }

    public function test_role_must_be_valid_value(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);
        $team->members()->attach($this->otherUser->id, ['role' => 'member']);

        $response = $this->actingAs($this->user)
            ->putJson("/api/teams/{$team->id}/members/{$this->otherUser->id}", [
                'role' => 'invalid_role',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_user_can_view_own_teams(): void
    {
        $team1 = Team::factory()->create(['owner_id' => $this->user->id]);
        $team1->members()->attach($this->user->id, ['role' => 'owner']);

        $team2 = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $team2->members()->attach($this->user->id, ['role' => 'member']);

        $team3 = Team::factory()->create(['owner_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/teams');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $team1->id])
            ->assertJsonFragment(['id' => $team2->id])
            ->assertJsonMissing(['id' => $team3->id]);
    }

    public function test_user_can_view_team_details(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);

        $response = $this->actingAs($this->user)
            ->getJson("/api/teams/{$team->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $team->id,
                'name' => $team->name,
                'description' => $team->description,
            ]);
    }

    public function test_user_cannot_view_other_team_details(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $team->members()->attach($this->otherUser->id, ['role' => 'owner']);

        $response = $this->actingAs($this->user)
            ->getJson("/api/teams/{$team->id}");

        $response->assertStatus(403);
    }

    public function test_team_owner_can_update_team_details(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);

        $updateData = [
            'name' => 'Updated Team Name',
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/teams/{$team->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Team Name',
                'description' => 'Updated description',
            ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Updated Team Name',
        ]);
    }

    public function test_team_member_cannot_update_team_details(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $team->members()->attach($this->user->id, ['role' => 'member']);

        $updateData = [
            'name' => 'Hacked Team Name',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/teams/{$team->id}", $updateData);

        $response->assertStatus(403);
    }

    public function test_team_owner_can_delete_team(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/teams/{$team->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Team deleted successfully',
            ]);

        $this->assertSoftDeleted('teams', [
            'id' => $team->id,
        ]);
    }

    public function test_only_team_owner_can_delete_team(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->otherUser->id]);
        $team->members()->attach($this->user->id, ['role' => 'admin']);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/teams/{$team->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'deleted_at' => null,
        ]);
    }

    public function test_invitation_requires_valid_email(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);

        $inviteData = [
            'email' => 'invalid-email',
            'role' => 'member',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/teams/{$team->id}/invite", $inviteData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_invitation_requires_valid_role(): void
    {
        $team = Team::factory()->create(['owner_id' => $this->user->id]);
        $team->members()->attach($this->user->id, ['role' => 'owner']);

        $inviteData = [
            'email' => 'newmember@example.com',
            'role' => 'invalid_role',
        ];

        $response = $this->actingAs($this->user)
            ->postJson("/api/teams/{$team->id}/invite", $inviteData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }
}
