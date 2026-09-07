import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";

export const metadata = { title: "Events" };

// Sample rows — replaced by GET /api/v1/events (discovery.md §4.1).
const events = [
  { slug: "sample-event-one", title: "Sample Live Show", meta: "Lagos · Dec 2026" },
  { slug: "sample-event-two", title: "Sample Festival Appearance", meta: "Abuja · Jan 2027" },
];

export default function EventsPage() {
  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Live</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Events &amp; Performances</h1>
      <div className="mt-10 grid gap-6 sm:grid-cols-2">
        {events.map((event) => (
          <Card key={event.slug} href={`/events/${event.slug}`} title={event.title} meta={event.meta} />
        ))}
      </div>
    </section>
  );
}
