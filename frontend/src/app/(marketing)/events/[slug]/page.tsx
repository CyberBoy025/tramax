import { notFound } from "next/navigation";
import { Kicker } from "@/components/ui/kicker";
import { LinkButton } from "@/components/ui/button";
import { apiGet, type EventItem } from "@/lib/api";

export default async function EventPage(props: PageProps<"/events/[slug]">) {
  const { slug } = await props.params;
  const event = await apiGet<EventItem>(`events/${slug}`);

  if (!event) notFound();

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Event</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">{event.title}</h1>
      <p className="mt-2 text-[var(--color-text-secondary)]">
        {[event.venue, event.city].filter(Boolean).join(", ") || "Venue TBA"}
        {event.event_date && ` · ${new Date(event.event_date).toLocaleDateString()}`}
      </p>
      {event.description && (
        <p className="mt-6 max-w-[56ch] text-[var(--color-text-secondary)]">{event.description}</p>
      )}
      {event.artists && event.artists.length > 0 && (
        <p className="mt-4 text-sm text-[var(--color-text-secondary)]">
          Featuring {event.artists.map((a) => a.artist_name).join(", ")}
        </p>
      )}
      <LinkButton href="/events" variant="secondary" className="mt-6">
        Back to Events
      </LinkButton>
    </section>
  );
}
