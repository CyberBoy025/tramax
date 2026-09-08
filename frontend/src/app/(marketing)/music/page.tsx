import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";
import { apiGet, type Release } from "@/lib/api";

export const metadata = { title: "Music" };

export default async function MusicPage() {
  const releases = (await apiGet<Release[]>("releases")) ?? [];

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Catalogue</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Music &amp; Releases</h1>
      <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {releases.length === 0 && (
          <p className="text-sm text-[var(--color-text-secondary)]">No releases published yet.</p>
        )}
        {releases.map((release) => (
          <Card
            key={release.slug}
            href={`/music/${release.slug}`}
            title={release.title}
            meta={release.artist ? `${release.type} · ${release.artist.artist_name}` : release.type}
          />
        ))}
      </div>
    </section>
  );
}
