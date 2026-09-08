import { notFound } from "next/navigation";
import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";
import { apiGet, type Artist } from "@/lib/api";

export default async function ArtistProfilePage(props: PageProps<"/artists/[slug]">) {
  const { slug } = await props.params;
  const artist = await apiGet<Artist>(`artists/${slug}`);

  if (!artist) notFound();

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Artist</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">{artist.artist_name}</h1>
      {artist.genre && <p className="mt-2 text-[var(--color-text-secondary)]">{artist.genre}</p>}
      {artist.biography && (
        <p className="mt-6 max-w-[56ch] text-[var(--color-text-secondary)]">{artist.biography}</p>
      )}

      <h2 className="mt-12 text-xl font-semibold">Releases</h2>
      <div className="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {(!artist.releases || artist.releases.length === 0) && (
          <p className="text-sm text-[var(--color-text-secondary)]">No releases published yet.</p>
        )}
        {artist.releases?.map((release) => (
          <Card key={release.slug} href={`/music/${release.slug}`} title={release.title} meta={release.type} />
        ))}
      </div>
    </section>
  );
}
