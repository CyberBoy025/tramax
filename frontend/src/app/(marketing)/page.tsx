import { Kicker } from "@/components/ui/kicker";
import { LinkButton } from "@/components/ui/button";
import { Card } from "@/components/ui/card";

const stats = [
  { value: "—", label: "Artists" },
  { value: "—", label: "Releases" },
  { value: "—", label: "Cities Played" },
  { value: "—", label: "Years Active" },
];

// Sample rows — replace with real artist/release data once Tramax supplies it (proposal §14).
const featuredArtists = [
  { slug: "sample-artist-one", name: "Sample Artist One", genre: "Afrobeats" },
  { slug: "sample-artist-two", name: "Sample Artist Two", genre: "Amapiano" },
  { slug: "sample-artist-three", name: "Sample Artist Three", genre: "R&B" },
];

export default function HomePage() {
  return (
    <>
      <section className="mx-auto max-w-(--layout-container-max) px-6 pt-16 pb-24 sm:pt-24">
        <Kicker>Tramax Entertainment</Kicker>
        <h1 className="mt-4 max-w-[18ch] text-5xl font-semibold sm:text-6xl">
          Discover. <span className="font-accent">Develop.</span> Promote.
        </h1>
        <p className="mt-6 max-w-[52ch] text-lg text-[var(--color-text-secondary)]">
          Tramax finds talent, builds careers, and takes music from the studio
          to the stage — artists, catalogue, rights, and revenue, in one
          place.
        </p>
        <div className="mt-8 flex flex-wrap gap-3">
          <LinkButton href="/artists">Explore Artists</LinkButton>
          <LinkButton href="/music" variant="secondary">
            Listen to Music
          </LinkButton>
        </div>

        {/* Placeholder for the full-bleed featured-artist hero image (ui.md §11) */}
        <div
          aria-hidden
          className="mt-16 flex aspect-[21/9] w-full items-center justify-center rounded-[var(--radius-lg)] border border-dashed border-[var(--color-border-strong)] bg-[var(--color-bg-raised)] text-sm text-[var(--color-text-muted)]"
        >
          Featured artist / release hero image
        </div>
      </section>

      <section className="border-y border-[var(--color-border-default)] bg-[var(--color-bg-raised)] px-6 py-16">
        <div className="mx-auto grid max-w-(--layout-container-max) grid-cols-2 gap-8 sm:grid-cols-4">
          {stats.map((stat) => (
            <div key={stat.label}>
              <p className="font-data text-3xl font-semibold sm:text-4xl">{stat.value}</p>
              <p className="mt-1 text-sm text-[var(--color-text-secondary)]">{stat.label}</p>
            </div>
          ))}
        </div>
      </section>

      <section className="mx-auto max-w-(--layout-container-max) px-6 py-24">
        <Kicker>Featured</Kicker>
        <h2 className="mt-3 text-3xl font-semibold sm:text-4xl">Artists on Tramax</h2>
        <div className="mt-8 grid gap-6 sm:grid-cols-3">
          {featuredArtists.map((artist) => (
            <Card
              key={artist.slug}
              href={`/artists/${artist.slug}`}
              title={artist.name}
              meta={artist.genre}
            />
          ))}
        </div>
      </section>

      <section className="mx-auto max-w-(--layout-container-max) px-6 pb-24">
        <div className="rounded-[var(--radius-lg)] bg-[var(--color-bg-raised)] p-10 sm:p-16">
          <Kicker>Join Tramax</Kicker>
          <h2 className="mt-3 max-w-[24ch] text-3xl font-semibold sm:text-4xl">
            Ready to take your music further?
          </h2>
          <p className="mt-4 max-w-[52ch] text-[var(--color-text-secondary)]">
            Submit your music and tell us about your project — our A&amp;R
            team reviews every application.
          </p>
          <LinkButton href="/artists#submit" className="mt-6">
            Submit Your Music
          </LinkButton>
        </div>
      </section>
    </>
  );
}
