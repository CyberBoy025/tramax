"use client";

import { useEffect, useState, type FormEvent } from "react";
import { authGet, authPost, authPatch, authDelete, getStoredAuth } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

type Artist = {
  id: number;
  artist_name: string;
  slug: string;
  genre: string | null;
  status: string;
  biography?: string | null;
  photo_url?: string | null;
};

const STATUSES = ["Active", "Development", "Inactive"];
const STATUS_BADGE: Record<string, "success" | "warning" | "info"> = {
  Active: "success",
  Development: "warning",
  Inactive: "info",
};

export default function AdminArtistsPage() {
  const [artists, setArtists] = useState<Artist[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [editing, setEditing] = useState<Artist | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const role = getStoredAuth()?.user.role ?? null;
  const canWrite = role === "Super Administrator" || role === "A&R / Artist Manager";
  const canDelete = role === "Super Administrator";

  function load() {
    document.title = "Artists · Tramax Admin";
    authGet<Artist[]>("admin/artists").then((data) => {
      setArtists(data);
      setLoaded(true);
    });
  }

  useEffect(load, []);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);

    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form));
    const body = Object.fromEntries(Object.entries(data).filter(([, v]) => v !== ""));

    const result = editing
      ? await authPatch(`admin/artists/${editing.id}`, body)
      : await authPost("admin/artists", body);

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
    const result = await authDelete(`admin/artists/${id}`);
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

  if (artists === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Artists</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Music Catalogue (discovery.md §3: Super
          Administrator, Management, or A&amp;R only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Artists</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Music Catalogue module — shows every status, unlike the public GET /api/v1/artists.
      </p>

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Name</th>
              <th className="px-4 py-3 font-semibold">Genre</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              {canWrite && <th className="px-4 py-3 font-semibold"></th>}
            </tr>
          </thead>
          <tbody>
            {artists.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={canWrite ? 4 : 3}>
                  No artists yet.
                </td>
              </tr>
            )}
            {artists.map((a) => (
              <tr key={a.id} className="border-b border-[var(--color-border-default)] last:border-0">
                <td className="px-4 py-3">{a.artist_name}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{a.genre ?? "—"}</td>
                <td className="px-4 py-3">
                  <Badge status={STATUS_BADGE[a.status] ?? "info"}>{a.status}</Badge>
                </td>
                {canWrite && (
                  <td className="px-4 py-3 flex gap-3">
                    <button
                      type="button"
                      onClick={() => setEditing(a)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      Edit
                    </button>
                    {canDelete && (
                      <button
                        type="button"
                        onClick={() => handleDelete(a.id)}
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
            <p className="font-semibold">{editing ? `Edit "${editing.artist_name}"` : "New Artist"}</p>
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
              Artist Name
              <input
                name="artist_name"
                required
                defaultValue={editing?.artist_name}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Genre
              <input
                name="genre"
                defaultValue={editing?.genre ?? ""}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium sm:col-span-2">
              Biography
              <textarea
                name="biography"
                rows={3}
                defaultValue={editing?.biography ?? ""}
                className="rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 py-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Photo URL
              <input
                name="photo_url"
                defaultValue={editing?.photo_url ?? ""}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Status
              <select
                name="status"
                defaultValue={editing?.status ?? "Active"}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              >
                {STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
              </select>
            </label>
            <div className="flex items-end sm:col-span-2">
              <Button type="submit" disabled={submitting}>
                {submitting ? "Saving…" : editing ? "Save Changes" : "Add Artist"}
              </Button>
            </div>
          </form>
          {error && <p className="mt-3 text-sm text-[var(--color-state-error)]">{error}</p>}
        </div>
      )}
    </div>
  );
}
