import { notFound } from "next/navigation";
import Link from "next/link";
import { Kicker } from "@/components/ui/kicker";
import { apiGet, type Release } from "@/lib/api";

export default async function ReleasePage(props: PageProps<"/music/[slug]">) {
  const { slug } = await props.params;
  const release = await apiGet<Release>(`releases/${slug}`);

  if (!release) notFound();

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>{release.type}</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">{release.title}</h1>
      {release.artist && (
        <p className="mt-2 text-[var(--color-text-secondary)]">
          By{" "}
          <Link href={`/artists/${release.artist.slug}`} className="underline">
            {release.artist.artist_name}
          </Link>
        </p>
      )}
      {release.release_date && (
        <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
          Released {new Date(release.release_date).toLocaleDateString()}
        </p>
      )}

      {release.tracks && release.tracks.length > 0 && (
        <>
          <h2 className="mt-12 text-xl font-semibold">Tracklist</h2>
          <ol className="mt-4 flex flex-col gap-2">
            {release.tracks
              .sort((a, b) => a.track_number - b.track_number)
              .map((track) => (
                <li
                  key={track.id}
                  className="flex items-center gap-3 border-b border-[var(--color-border-default)] py-2 text-sm"
                >
                  <span className="font-data text-[var(--color-text-muted)]">{track.track_number}</span>
                  {track.title}
                </li>
              ))}
          </ol>
        </>
      )}
    </section>
  );
}
