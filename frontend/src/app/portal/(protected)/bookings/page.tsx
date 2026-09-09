"use client";

import { useEffect, useState } from "react";
import { authGet } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";

type PortalEvent = {
  id: number;
  title: string;
  venue: string | null;
  city: string | null;
  event_date: string | null;
  status: string;
};

const STATUS_BADGE: Record<string, "success" | "warning" | "error" | "info"> = {
  Upcoming: "info",
  Completed: "success",
  Cancelled: "error",
};

export default function PortalBookingsPage() {
  const [events, setEvents] = useState<PortalEvent[] | null>(null);
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    document.title = "Bookings · Tramax Portal";
    authGet<PortalEvent[]>("portal/events").then((data) => {
      setEvents(data);
      setLoaded(true);
    });
  }, []);

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (events === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Bookings</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your account isn&apos;t linked to a public artist profile yet.
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Bookings</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Events you&apos;re booked on. Read-only — an A&amp;R Manager or Super Admin manages event
        details and lineup (discovery.md §3: Events &amp; Bookings is Own (read) for artists).
      </p>

      <div className="mt-[var(--space-lg)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Event</th>
              <th className="px-4 py-3 font-semibold">Venue</th>
              <th className="px-4 py-3 font-semibold">Date</th>
              <th className="px-4 py-3 font-semibold">Status</th>
            </tr>
          </thead>
          <tbody>
            {events.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={4}>
                  No bookings yet.
                </td>
              </tr>
            )}
            {events.map((e) => (
              <tr key={e.id} className="border-b border-[var(--color-border-default)] last:border-0">
                <td className="px-4 py-3">{e.title}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                  {[e.venue, e.city].filter(Boolean).join(", ") || "—"}
                </td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                  {e.event_date ? new Date(e.event_date).toLocaleDateString() : "—"}
                </td>
                <td className="px-4 py-3">
                  <Badge status={STATUS_BADGE[e.status] ?? "info"}>{e.status}</Badge>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
