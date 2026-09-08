import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";
import { EnquiryForm } from "@/components/marketing/enquiry-form";
import { apiGet, type Artist } from "@/lib/api";

export const metadata = { title: "Artists" };

export default async function ArtistsPage() {
  const artists = (await apiGet<Artist[]>("artists")) ?? [];

  return (
    <>
      <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
        <Kicker>Roster</Kicker>
        <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Artists</h1>
        <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          {artists.length === 0 && (
            <p className="text-sm text-[var(--color-text-secondary)]">No artists published yet.</p>
          )}
          {artists.map((artist) => (
            <Card
              key={artist.slug}
              href={`/artists/${artist.slug}`}
              title={artist.artist_name}
              meta={artist.genre ?? undefined}
            />
          ))}
        </div>
      </section>

      <section id="submit" className="mx-auto max-w-(--layout-container-max) px-6 pb-24">
        <div className="rounded-[var(--radius-lg)] bg-[var(--color-bg-raised)] p-10 sm:p-16">
          <Kicker>Join the roster</Kicker>
          <h2 className="mt-3 text-3xl font-semibold sm:text-4xl">Submit your music</h2>
          <p className="mt-4 max-w-[56ch] text-[var(--color-text-secondary)]">
            Tell us about yourself and your music — our A&amp;R team reviews
            every application (Submitted → Under Review → Shortlisted →
            Accepted).
          </p>
          <div className="mt-8">
            <EnquiryForm
              endpoint="applications"
              submitLabel="Submit Application"
              fields={[
                { name: "full_name", label: "Full name" },
                { name: "artist_name", label: "Artist name" },
                { name: "email", label: "Email", type: "email" },
                { name: "genre", label: "Genre" },
                { name: "biography", label: "Tell us about you", type: "textarea" },
              ]}
            />
          </div>
        </div>
      </section>
    </>
  );
}
