import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";
import { apiGet, type NewsPost } from "@/lib/api";

export const metadata = { title: "News" };

export default async function NewsPage() {
  const posts = (await apiGet<NewsPost[]>("news")) ?? [];

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Newsroom</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">News</h1>
      <div className="mt-10 grid gap-6 sm:grid-cols-2">
        {posts.length === 0 && (
          <p className="text-sm text-[var(--color-text-secondary)]">No news posts published yet.</p>
        )}
        {posts.map((post) => (
          <Card
            key={post.slug}
            href={`/news/${post.slug}`}
            title={post.title}
            meta={post.published_at ? new Date(post.published_at).toLocaleDateString() : undefined}
          />
        ))}
      </div>
    </section>
  );
}
