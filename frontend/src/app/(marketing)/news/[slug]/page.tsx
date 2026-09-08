import { notFound } from "next/navigation";
import { Kicker } from "@/components/ui/kicker";
import { apiGet, type NewsPost } from "@/lib/api";

export default async function NewsPostPage(props: PageProps<"/news/[slug]">) {
  const { slug } = await props.params;
  const post = await apiGet<NewsPost>(`news/${slug}`);

  if (!post) notFound();

  return (
    <article className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>News</Kicker>
      <h1 className="mt-3 max-w-[28ch] text-4xl font-semibold sm:text-5xl">{post.title}</h1>
      {post.published_at && (
        <p className="mt-2 text-sm text-[var(--color-text-secondary)]">
          {new Date(post.published_at).toLocaleDateString()}
        </p>
      )}
      <div className="mt-6 max-w-[64ch] whitespace-pre-line text-[var(--color-text-secondary)]">
        {post.body || "Full article content coming soon."}
      </div>
    </article>
  );
}
