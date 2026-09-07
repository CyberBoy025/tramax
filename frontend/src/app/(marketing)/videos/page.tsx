import { Kicker } from "@/components/ui/kicker";

export const metadata = { title: "Videos" };

const categories = ["Official Videos", "Lyric Videos", "Behind The Scenes", "Interviews", "Live Performances"];

export default function VideosPage() {
  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Media</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Videos &amp; Media</h1>
      <div className="mt-6 flex flex-wrap gap-2">
        {categories.map((category) => (
          <span
            key={category}
            className="rounded-[var(--radius-pill)] border border-[var(--color-border-default)] px-3 py-1 text-xs text-[var(--color-text-secondary)]"
          >
            {category}
          </span>
        ))}
      </div>
      <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        {[1, 2, 3].map((n) => (
          <div
            key={n}
            aria-hidden
            className="flex aspect-video items-center justify-center rounded-[var(--radius-md)] border border-dashed border-[var(--color-border-strong)] bg-[var(--color-bg-raised)] text-sm text-[var(--color-text-muted)]"
          >
            Video embed placeholder
          </div>
        ))}
      </div>
    </section>
  );
}
