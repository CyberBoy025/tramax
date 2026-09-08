import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";
import { apiGet, type EventItem } from "@/lib/api";

export const metadata = { title: "Events" };

function formatEventMeta(event: EventItem): string {
  const parts = [event.city, event.event_date ? new Date(event.event_date).toLocaleDateString() : null];
  return parts.filter(Boolean).join(" · ") || "Date TBA";
}

export default async function EventsPage() {
  const events = (await apiGet<EventItem[]>("events")) ?? [];

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Live</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Events &amp; Performances</h1>
      <div className="mt-10 grid gap-6 sm:grid-cols-2">
        {events.length === 0 && (
          <p className="text-sm text-[var(--color-text-secondary)]">No events scheduled yet.</p>
        )}
        {events.map((event) => (
          <Card
            key={event.slug}
            href={`/events/${event.slug}`}
            title={event.title}
            meta={formatEventMeta(event)}
          />
        ))}
      </div>
    </section>
  );
}
