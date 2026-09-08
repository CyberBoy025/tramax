"use client";

import { useEffect, useState, type FormEvent } from "react";
import { authGet, authPost, authPatch, authDelete, getStoredAuth } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

type ArtistRef = { id: number; artist_name: string; slug: string };

type Event = {
  id: number;
  title: string;
  slug: string;
  venue: string | null;
  city: string | null;
  event_date: string | null;
  description: string | null;
  ticket_link: string | null;
  status: string;
  artists: ArtistRef[];
};

const STATUSES = ["Upcoming", "Completed", "Cancelled"];
const STATUS_BADGE: Record<string, "success" | "warning" | "info" | "error"> = {
  Upcoming: "success",
  Completed: "info",
  Cancelled: "error",
};

// datetime-local wants "YYYY-MM-DDTHH:mm" — ISO strings from the API carry
// seconds/millis/Z, so trim rather than reformat.
function toDatetimeLocal(iso: string | null): string {
  if (!iso) return "";
  return iso.slice(0, 16);
}

export default function AdminEventsPage() {
  const [events, setEvents] = useState<Event[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [artists, setArtists] = useState<ArtistRef[]>([]);
  const [editing, setEditing] = useState<Event | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const role = getStoredAuth()?.user.role ?? null;
  const canWrite = role === "Super Administrator" || role === "A&R / Artist Manager";
  const canDelete = role === "Super Administrator";

  function load() {
    document.title = "Events · Tramax Admin";
    authGet<Event[]>("admin/events").then((data) => {
      setEvents(data);
      setLoaded(true);
    });
  }

  useEffect(() => {
    load();
    authGet<ArtistRef[]>("admin/artists").then((a) => setArtists(a ?? []));
  }, []);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);

    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form));
    const artist_profile_ids = [...form.querySelectorAll<HTMLInputElement>('input[name="artist"]:checked')].map(
      (el) => Number(el.value)
    );
    const body = {
      ...Object.fromEntries(Object.entries(data).filter(([k, v]) => v !== "" && k !== "artist")),
      artist_profile_ids,
    };

    const result = editing
      ? await authPatch(`admin/events/${editing.id}`, body)
      : await authPost("admin/events", body);

    setSubmitting(false);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    form.reset();
    setEditing(null);
    load();
  }

  async function handleDelete(id: number) {
    const result = await authDelete(`admin/events/${id}`);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    if (editing?.id === id) setEditing(null);
    load();
  }

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (events === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Events</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Events &amp; Bookings (discovery.md §3: Super
          Administrator, Management, A&amp;R, or Content Manager only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Events</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Shows every status, including Cancelled, unlike the public GET /api/v1/events.
      </p>

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Title</th>
              <th className="px-4 py-3 font-semibold">Venue</th>
              <th className="px-4 py-3 font-semibold">Date</th>
              <th className="px-4 py-3 font-semibold">Artists</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              {canWrite && <th className="px-4 py-3 font-semibold"></th>}
            </tr>
          </thead>
          <tbody>
            {events.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={canWrite ? 6 : 5}>
                  No events yet.
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
                  {e.event_date ? new Date(e.event_date).toLocaleString() : "—"}
                </td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                  {e.artists.length ? e.artists.map((a) => a.artist_name).join(", ") : "—"}
                </td>
                <td className="px-4 py-3">
                  <Badge status={STATUS_BADGE[e.status] ?? "info"}>{e.status}</Badge>
                </td>
                {canWrite && (
                  <td className="px-4 py-3 flex gap-3">
                    <button
                      type="button"
                      onClick={() => setEditing(e)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      Edit
                    </button>
                    {canDelete && (
                      <button
                        type="button"
                        onClick={() => handleDelete(e.id)}
                        className="text-xs text-[var(--color-state-error)] hover:underline"
                      >
                        Delete
                      </button>
                    )}
                  </td>
                )}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {canWrite && (
        <div className="mt-[var(--space-xl)] rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
          <div className="flex items-center justify-between">
            <p className="font-semibold">{editing ? `Edit "${editing.title}"` : "New Event"}</p>
            {editing && (
              <button
                type="button"
                onClick={() => setEditing(null)}
                className="text-xs text-[var(--color-text-secondary)] hover:underline"
              >
                Cancel
              </button>
            )}
          </div>
          <form key={editing?.id ?? "new"} onSubmit={handleSubmit} className="mt-[var(--space-md)] grid gap-[var(--space-md)] sm:grid-cols-2">
            <label className="flex flex-col gap-1 text-xs font-medium">
              Title
              <input
                name="title"
                required
                defaultValue={editing?.title}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Venue
              <input
                name="venue"
                defaultValue={editing?.venue ?? ""}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              City
              <input
                name="city"
                defaultValue={editing?.city ?? ""}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Date &amp; Time
              <input
                type="datetime-local"
                name="event_date"
                defaultValue={toDatetimeLocal(editing?.event_date ?? null)}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Ticket Link
              <input
                name="ticket_link"
                defaultValue={editing?.ticket_link ?? ""}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Status
              <select
                name="status"
                defaultValue={editing?.status ?? "Upcoming"}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              >
                {STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
              </select>
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium sm:col-span-2">
              Description
              <textarea
                name="description"
                rows={3}
                defaultValue={editing?.description ?? ""}
                className="rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 py-2 text-sm"
              />
            </label>
            <div className="sm:col-span-2">
              <p className="text-xs font-medium">Participating Artists</p>
              <div className="mt-2 flex flex-wrap gap-3">
                {artists.map((a) => (
                  <label key={a.id} className="flex items-center gap-1.5 text-xs text-[var(--color-text-secondary)]">
                    <input
                      type="checkbox"
                      name="artist"
                      value={a.id}
                      defaultChecked={editing?.artists.some((ea) => ea.id === a.id)}
                    />
                    {a.artist_name}
                  </label>
                ))}
              </div>
            </div>
            <div className="flex items-end sm:col-span-2">
              <Button type="submit" disabled={submitting}>
                {submitting ? "Saving…" : editing ? "Save Changes" : "Add Event"}
              </Button>
            </div>
          </form>
          {error && <p className="mt-3 text-sm text-[var(--color-state-error)]">{error}</p>}
        </div>
      )}
    </div>
  );
}
