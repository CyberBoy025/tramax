<?php

namespace Database\Seeders;

use App\Models\ArtistProfile;
use App\Models\Event;
use App\Models\NewsPost;
use App\Models\Release;
use Illuminate\Database\Seeder;

// Seeds the same sample rows the frontend previously hardcoded as placeholders
// (see frontend/src/app/(marketing)/*), so wiring up the API doesn't change
// what the site shows — it's still clearly-labeled sample data, now served
// from the database instead of a hardcoded array.
class PublicSiteSeeder extends Seeder
{
    public function run(): void
    {
        $artists = [
            ['artist_name' => 'Sample Artist One', 'slug' => 'sample-artist-one', 'genre' => 'Afrobeats'],
            ['artist_name' => 'Sample Artist Two', 'slug' => 'sample-artist-two', 'genre' => 'Amapiano'],
            ['artist_name' => 'Sample Artist Three', 'slug' => 'sample-artist-three', 'genre' => 'R&B'],
            ['artist_name' => 'Sample Artist Four', 'slug' => 'sample-artist-four', 'genre' => 'Hip-Hop'],
        ];

        foreach ($artists as $artist) {
            ArtistProfile::updateOrCreate(['slug' => $artist['slug']], $artist + ['status' => 'Active']);
        }

        $releases = [
            ['title' => 'Sample Single One', 'slug' => 'sample-single-one', 'type' => 'Single', 'artist' => 'sample-artist-one'],
            ['title' => 'Sample EP One', 'slug' => 'sample-ep-one', 'type' => 'EP', 'artist' => 'sample-artist-two'],
            ['title' => 'Sample Album One', 'slug' => 'sample-album-one', 'type' => 'Album', 'artist' => 'sample-artist-three'],
        ];

        foreach ($releases as $release) {
            $artistId = ArtistProfile::where('slug', $release['artist'])->value('id');
            Release::updateOrCreate(
                ['slug' => $release['slug']],
                [
                    'title' => $release['title'],
                    'type' => $release['type'],
                    'artist_profile_id' => $artistId,
                    'status' => 'Published',
                    'release_date' => now()->subDays(random_int(10, 200)),
                ]
            );
        }

        $events = [
            ['title' => 'Sample Live Show', 'slug' => 'sample-event-one', 'venue' => 'Sample Venue', 'city' => 'Lagos', 'event_date' => now()->addMonths(3)],
            ['title' => 'Sample Festival Appearance', 'slug' => 'sample-event-two', 'venue' => 'Sample Arena', 'city' => 'Abuja', 'event_date' => now()->addMonths(4)],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(['slug' => $event['slug']], $event + ['status' => 'Upcoming']);
        }

        $posts = [
            ['title' => 'Sample Signing Announcement', 'slug' => 'sample-post-one'],
            ['title' => 'Sample Release Recap', 'slug' => 'sample-post-two'],
        ];

        foreach ($posts as $post) {
            NewsPost::updateOrCreate(
                ['slug' => $post['slug']],
                $post + ['status' => 'Published', 'published_at' => now()->subDays(random_int(1, 60))]
            );
        }
    }
}
