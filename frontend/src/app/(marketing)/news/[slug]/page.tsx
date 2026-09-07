import { Kicker } from "@/components/ui/kicker";

// Replaced by GET /api/v1/news/{slug} (discovery.md §4.1) once the backend exists.
export default async function NewsPostPage(props: PageProps<"/news/[slug]">) {
  const { slug } = await props.params;

  return (
    <article className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>News</Kicker>
      <h1 className="mt-3 max-w-[28ch] text-4xl font-semibold sm:text-5xl capitalize">
        {slug.replace(/-/g, " ")}
      </h1>
      <p className="mt-6 max-w-[64ch] text-[var(--color-text-secondary)]">
        Placeholder article body — renders once content is published through
        the admin CMS (§4.3).
      </p>
    </article>
  );
}
