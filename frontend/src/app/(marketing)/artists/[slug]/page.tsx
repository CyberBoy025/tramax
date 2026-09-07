import { Kicker } from "@/components/ui/kicker";

// Replaced by GET /api/v1/artists/{slug} (discovery.md §4.1) once the backend exists.
export default async function ArtistProfilePage(props: PageProps<"/artists/[slug]">) {
  const { slug } = await props.params;

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Artist</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl capitalize">
        {slug.replace(/-/g, " ")}
      </h1>
      <p className="mt-6 max-w-[56ch] text-[var(--color-text-secondary)]">
        Placeholder profile — biography, genre, discography, music videos,
        photos, upcoming performances, socials, and booking info render here
        once artist data exists (§4.1).
      </p>
    </section>
  );
}
