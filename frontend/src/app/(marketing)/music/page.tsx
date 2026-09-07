import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";

export const metadata = { title: "Music" };

// Sample rows — replaced by GET /api/v1/releases (discovery.md §4.1).
const releases = [
  { slug: "sample-single-one", title: "Sample Single One", type: "Single" },
  { slug: "sample-ep-one", title: "Sample EP One", type: "EP" },
  { slug: "sample-album-one", title: "Sample Album One", type: "Album" },
];

export default function MusicPage() {
  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Catalogue</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Music &amp; Releases</h1>
      <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {releases.map((release) => (
          <Card
            key={release.slug}
            href={`/music/${release.slug}`}
            title={release.title}
            meta={release.type}
          />
        ))}
      </div>
    </section>
  );
}
