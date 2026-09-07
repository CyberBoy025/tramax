import { Kicker } from "@/components/ui/kicker";

// Replaced by GET /api/v1/releases/{slug} (discovery.md §4.1) once the backend exists.
export default async function ReleasePage(props: PageProps<"/music/[slug]">) {
  const { slug } = await props.params;

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Release</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl capitalize">
        {slug.replace(/-/g, " ")}
      </h1>
      <p className="mt-6 max-w-[56ch] text-[var(--color-text-secondary)]">
        Placeholder release page — artist, release date, genre, and streaming
        links (Spotify, Apple Music, YouTube Music, Audiomack, Boomplay)
        render here once release data exists (§4.1).
      </p>
    </section>
  );
}
