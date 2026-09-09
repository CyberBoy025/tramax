<?php

namespace Tests\Feature;

use App\Models\ArtistProfile;
use App\Models\Product;
use App\Models\Release;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_artist_listing_excludes_inactive(): void
    {
        ArtistProfile::factory()->create(['artist_name' => 'Visible Artist', 'status' => 'Active']);
        ArtistProfile::factory()->create(['artist_name' => 'Hidden Artist', 'status' => 'Inactive']);
        ArtistProfile::factory()->create(['artist_name' => 'Dev Artist', 'status' => 'Development']);

        $response = $this->getJson('/api/v1/artists');

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('artist_name');
        $this->assertTrue($names->contains('Visible Artist'));
        $this->assertTrue($names->contains('Dev Artist'));
        $this->assertFalse($names->contains('Hidden Artist'));
    }

    public function test_public_release_listing_only_shows_published(): void
    {
        Release::factory()->create(['title' => 'Live Single', 'status' => 'Published']);
        Release::factory()->create(['title' => 'Draft Single', 'status' => 'Draft']);
        Release::factory()->create(['title' => 'Processing Single', 'status' => 'Processing']);

        $response = $this->getJson('/api/v1/releases');

        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('Live Single'));
        $this->assertFalse($titles->contains('Draft Single'));
        $this->assertFalse($titles->contains('Processing Single'));
    }

    public function test_public_store_listing_only_shows_published(): void
    {
        Product::factory()->create(['title' => 'Live Hoodie', 'status' => 'Published']);
        Product::factory()->create(['title' => 'Draft Hoodie', 'status' => 'Draft']);

        $response = $this->getJson('/api/v1/products');

        $titles = collect($response->json('data'))->pluck('title');
        $this->assertTrue($titles->contains('Live Hoodie'));
        $this->assertFalse($titles->contains('Draft Hoodie'));
    }

    public function test_draft_product_is_not_reachable_by_slug(): void
    {
        $draft = Product::factory()->create(['status' => 'Draft']);

        $this->getJson("/api/v1/products/{$draft->slug}")->assertStatus(404);
    }

    public function test_artist_application_can_be_submitted_without_auth(): void
    {
        $response = $this->postJson('/api/v1/applications', [
            'full_name' => 'Jane Doe',
            'artist_name' => 'Jane D',
            'email' => 'jane@example.test',
            'genre' => 'Afrobeats',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('artist_applications', ['email' => 'jane@example.test', 'status' => 'Submitted']);
    }

    public function test_artist_application_requires_required_fields(): void
    {
        $this->postJson('/api/v1/applications', ['email' => 'incomplete@example.test'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['full_name', 'artist_name']);
    }

    public function test_licensing_request_can_be_submitted_without_auth(): void
    {
        $response = $this->postJson('/api/v1/licensing-requests', [
            'company_name' => 'Acme Films',
            'contact_person' => 'John Smith',
            'email' => 'john@acmefilms.test',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('licensing_requests', ['email' => 'john@acmefilms.test', 'status' => 'New']);
    }

    public function test_partner_enquiry_can_be_submitted_without_auth(): void
    {
        $response = $this->postJson('/api/v1/partners', [
            'organization_name' => 'Acme Distro',
            'contact_person' => 'Sam Lee',
            'email' => 'sam@acmedistro.test',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('partners', ['email' => 'sam@acmedistro.test', 'status' => 'New']);
    }
}
