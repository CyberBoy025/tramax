<?php

namespace Tests\Feature\Admin;

use App\Models\ArtistProfile;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Concerns\CreatesRoleUsers;
use Tests\TestCase;

class ArtistManagementCrudTest extends TestCase
{
    use CreatesRoleUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        Sanctum::actingAs($this->superAdmin());
    }

    public function test_accepting_an_application_provisions_a_public_artist_profile(): void
    {
        $applicationId = $this->postJson('/api/v1/applications', [
            'full_name' => 'Ada Lovelace',
            'artist_name' => 'Ada L',
            'email' => 'ada@example.test',
            'genre' => 'Afrobeats',
        ])->json('data.id');

        $this->assertDatabaseMissing('artist_profiles', ['artist_name' => 'Ada L']);

        $this->patchJson("/api/v1/admin/applications/{$applicationId}/status", ['status' => 'Accepted'])
            ->assertOk()
            ->assertJsonPath('data.status', 'Accepted');

        $this->assertDatabaseHas('artist_profiles', ['artist_name' => 'Ada L', 'status' => 'Development']);
    }

    public function test_updating_status_to_an_invalid_value_is_rejected(): void
    {
        $applicationId = $this->postJson('/api/v1/applications', [
            'full_name' => 'Bad Status', 'artist_name' => 'BS', 'email' => 'bs@example.test',
        ])->json('data.id');

        $this->patchJson("/api/v1/admin/applications/{$applicationId}/status", ['status' => 'Not A Real Status'])
            ->assertStatus(422);
    }

    public function test_admin_index_returns_every_artist_status_unlike_public_listing(): void
    {
        ArtistProfile::factory()->create(['artist_name' => 'Inactive One', 'status' => 'Inactive']);

        $this->getJson('/api/v1/admin/artists')
            ->assertOk()
            ->assertJsonFragment(['artist_name' => 'Inactive One']);
    }

    public function test_creating_an_event_syncs_linked_artists(): void
    {
        $artist = ArtistProfile::factory()->create();

        $eventId = $this->postJson('/api/v1/admin/events', [
            'title' => 'Lagos Live Show',
            'artist_profile_ids' => [$artist->id],
        ])->assertCreated()->json('data.id');

        $event = Event::with('artists')->findOrFail($eventId);
        $this->assertCount(1, $event->artists);
        $this->assertSame($artist->id, $event->artists->first()->id);
    }

    public function test_updating_an_events_artist_list_replaces_the_previous_links(): void
    {
        $first = ArtistProfile::factory()->create();
        $second = ArtistProfile::factory()->create();

        $eventId = $this->postJson('/api/v1/admin/events', [
            'title' => 'Abuja Tour Stop', 'artist_profile_ids' => [$first->id],
        ])->json('data.id');

        $this->patchJson("/api/v1/admin/events/{$eventId}", ['artist_profile_ids' => [$second->id]])->assertOk();

        $event = Event::with('artists')->findOrFail($eventId);
        $this->assertCount(1, $event->artists);
        $this->assertSame($second->id, $event->artists->first()->id);
    }
}
