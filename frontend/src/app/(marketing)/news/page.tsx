import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";

export const metadata = { title: "News" };

// Sample rows — replaced by GET /api/v1/news (discovery.md §4.1).
const posts = [
  { slug: "sample-post-one", title: "Sample Signing Announcement", meta: "News" },
  { slug: "sample-post-two", title: "Sample Release Recap", meta: "News" },
];

export default function NewsPage() {
  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Newsroom</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">News</h1>
      <div className="mt-10 grid gap-6 sm:grid-cols-2">
        {posts.map((post) => (
          <Card key={post.slug} href={`/news/${post.slug}`} title={post.title} meta={post.meta} />
        ))}
      </div>
    </section>
  );
}
