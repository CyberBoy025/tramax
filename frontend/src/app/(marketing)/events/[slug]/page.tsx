import { Kicker } from "@/components/ui/kicker";
import { LinkButton } from "@/components/ui/button";

// Replaced by GET /api/v1/events/{slug} (discovery.md §4.1) once the backend exists.
export default async function EventPage(props: PageProps<"/events/[slug]">) {
  const { slug } = await props.params;

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Event</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl capitalize">
        {slug.replace(/-/g, " ")}
      </h1>
      <p className="mt-6 max-w-[56ch] text-[var(--color-text-secondary)]">
        Placeholder event page — venue, date, description, participating
        artists, and a ticket link render here once event data exists (§4.1).
      </p>
      <LinkButton href="/events" variant="secondary" className="mt-6">
        Back to Events
      </LinkButton>
    </section>
  );
}
