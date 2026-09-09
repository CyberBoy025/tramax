<?php

namespace Tests\Feature\Portal;

use App\Models\ArtistProfile;
use App\Models\Event;
use App\Models\PortalNotification;
use App\Models\Release;
use App\Models\Role;
use App\Models\RoyaltyStatement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_non_artist_roles_are_denied_every_portal_route(): void
    {
        foreach ([Role::SUPER_ADMIN, Role::MANAGEMENT, Role::AR_MANAGER, Role::FINANCE, Role::CONTENT_MANAGER, Role::PARTNER] as $roleName) {
            Sanctum::actingAs($this->userWithRole($roleName));
            $this->getJson('/api/v1/portal/profile')->assertStatus(403);
            $this->getJson('/api/v1/portal/releases')->assertStatus(403);
        }
    }

    public function test_unauthenticated_requests_are_rejected(): void
    {
        $this->getJson('/api/v1/portal/profile')->assertStatus(401);
    }

    public function test_artist_without_a_linked_profile_gets_empty_lists_not_errors(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::ARTIST));

        $this->getJson('/api/v1/portal/profile')->assertOk()->assertJsonPath('data', null);
        $this->getJson('/api/v1/portal/releases')->assertOk()->assertJsonPath('data', []);
        $this->getJson('/api/v1/portal/royalty-statements')->assertOk()->assertJsonPath('data', []);
        $this->getJson('/api/v1/portal/events')->assertOk()->assertJsonPath('data', []);
    }

    public function test_artist_without_a_profile_cannot_update_or_submit(): void
    {
        Sanctum::actingAs($this->userWithRole(Role::ARTIST));

        $this->patchJson('/api/v1/portal/profile', ['genre' => 'Afrobeats'])->assertStatus(422);
        $this->postJson('/api/v1/portal/releases', ['title' => 'Test', 'type' => 'Single'])->assertStatus(422);
    }

    public function test_artist_can_view_and_update_their_own_profile(): void
    {
        [$user, $profile] = $this->artistWithProfile();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/portal/profile')->assertOk()->assertJsonPath('data.id', $profile->id);

        $this->patchJson('/api/v1/portal/profile', ['genre' => 'Afrobeats', 'biography' => 'Updated bio'])
            ->assertOk()
            ->assertJsonPath('data.genre', 'Afrobeats');

        $this->assertDatabaseHas('artist_profiles', ['id' => $profile->id, 'genre' => 'Afrobeats']);
    }

    public function test_artist_cannot_change_their_own_name_slug_or_status_via_portal(): void
    {
        [$user, $profile] = $this->artistWithProfile();
        Sanctum::actingAs($user);

        $this->patchJson('/api/v1/portal/profile', [
            'artist_name' => 'Hijacked Name',
            'status' => 'Inactive',
        ])->assertOk();

        $fresh = $profile->fresh();
        $this->assertSame($profile->artist_name, $fresh->artist_name);
        $this->assertSame('Active', $fresh->status);
    }

    public function test_artist_can_submit_a_release_and_it_is_forced_to_draft(): void
    {
        [$user, $profile] = $this->artistWithProfile();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/portal/releases', [
            'title' => 'My New Single',
            'type' => 'Single',
            'status' => 'Published',
        ]);

        $response->assertCreated()->assertJsonPath('data.status', 'Draft');
        $this->assertDatabaseHas('releases', [
            'title' => 'My New Single', 'artist_profile_id' => $profile->id, 'status' => 'Draft',
        ]);
    }

    public function test_artist_only_sees_their_own_releases_not_another_artists(): void
    {
        [$user, $profile] = $this->artistWithProfile();
        Release::factory()->create(['artist_profile_id' => $profile->id, 'title' => 'Mine']);

        $otherProfile = ArtistProfile::factory()->create();
        Release::factory()->create(['artist_profile_id' => $otherProfile->id, 'title' => 'Not Mine']);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v1/portal/releases')->assertOk();

        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('Mine'));
        $this->assertFalse($titles->contains('Not Mine'));
    }

    public function test_artist_only_sees_their_own_royalty_statements(): void
    {
        [$user, $profile] = $this->artistWithProfile();
        RoyaltyStatement::factory()->create(['artist_profile_id' => $profile->id, 'total_revenue' => 500]);

        $otherProfile = ArtistProfile::factory()->create();
        RoyaltyStatement::factory()->create(['artist_profile_id' => $otherProfile->id, 'total_revenue' => 9999]);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v1/portal/royalty-statements')->assertOk();

        $revenues = collect($response->json('data'))->pluck('total_revenue');
        $this->assertCount(1, $revenues);
        $this->assertEquals(500, $revenues->first());
    }

    public function test_artist_only_sees_events_they_are_linked_to(): void
    {
        [$user, $profile] = $this->artistWithProfile();
        $myEvent = Event::factory()->create(['title' => 'My Show']);
        $myEvent->artists()->attach($profile->id);

        Event::factory()->create(['title' => 'Someone Elses Show']);

        Sanctum::actingAs($user);
        $response = $this->getJson('/api/v1/portal/events')->assertOk();

        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('My Show'));
        $this->assertFalse($titles->contains('Someone Elses Show'));
    }

    public function test_artist_only_sees_and_can_only_mark_read_their_own_notifications(): void
    {
        [$user] = $this->artistWithProfile();
        $mine = PortalNotification::factory()->create(['user_id' => $user->id, 'title' => 'For me']);

        $otherUser = $this->userWithRole(Role::ARTIST);
        $notMine = PortalNotification::factory()->create(['user_id' => $otherUser->id, 'title' => 'Not for me']);

        Sanctum::actingAs($user);
        $index = $this->getJson('/api/v1/portal/notifications')->assertOk();
        $titles = collect($index->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('For me'));
        $this->assertFalse($titles->contains('Not for me'));

        $this->patchJson("/api/v1/portal/notifications/{$mine->id}/read")->assertOk();
        $this->assertNotNull($mine->fresh()->read_at);

        $this->patchJson("/api/v1/portal/notifications/{$notMine->id}/read")->assertStatus(404);
        $this->assertNull($notMine->fresh()->read_at);
    }
}
