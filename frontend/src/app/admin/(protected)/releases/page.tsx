"use client";

import { useEffect, useState, type FormEvent } from "react";
import { authGet, authPost, authPatch, authDelete, getStoredAuth, publicGet } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

type Release = {
  id: number;
  title: string;
  slug: string;
  type: string;
  release_date: string | null;
  status?: string;
  artist: { id: number; artist_name: string; slug: string };
};

type ArtistOption = { id: number; artist_name: string };

const TYPES = ["Single", "EP", "Album"];
const STATUSES = ["Draft", "Processing", "Published"];
const STATUS_BADGE: Record<string, "success" | "warning" | "info"> = {
  Published: "success",
  Processing: "warning",
  Draft: "info",
};

export default function AdminReleasesPage() {
  const [releases, setReleases] = useState<Release[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [artists, setArtists] = useState<ArtistOption[]>([]);
  const [editing, setEditing] = useState<Release | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const role = getStoredAuth()?.user.role ?? null;
  const canWrite = role === "Super Administrator" || role === "A&R / Artist Manager";
  const canDelete = role === "Super Administrator";

  function load() {
    document.title = "Releases · Tramax Admin";
    authGet<Release[]>("admin/releases").then((data) => {
      setReleases(data);
      setLoaded(true);
    });
  }

  useEffect(() => {
    load();
    publicGet<ArtistOption[]>("artists").then((a) => setArtists(a ?? []));
  }, []);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);

    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form));
    const body = Object.fromEntries(Object.entries(data).filter(([, v]) => v !== ""));

    const result = editing
      ? await authPatch(`admin/releases/${editing.id}`, body)
      : await authPost("admin/releases", body);

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
    const result = await authDelete(`admin/releases/${id}`);
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

  if (releases === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Releases</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Music Catalogue (discovery.md §3: Super
          Administrator, Management, or A&amp;R only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Releases</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Music Catalogue module — shows every status, unlike the public GET /api/v1/releases.
      </p>

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Title</th>
              <th className="px-4 py-3 font-semibold">Artist</th>
              <th className="px-4 py-3 font-semibold">Type</th>
              <th className="px-4 py-3 font-semibold">Release Date</th>
              {canWrite && <th className="px-4 py-3 font-semibold"></th>}
            </tr>
          </thead>
          <tbody>
            {releases.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={canWrite ? 5 : 4}>
                  No releases yet.
                </td>
              </tr>
            )}
            {releases.map((r) => (
              <tr key={r.id} className="border-b border-[var(--color-border-default)] last:border-0">
                <td className="px-4 py-3">{r.title}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{r.artist?.artist_name ?? "—"}</td>
                <td className="px-4 py-3">
                  <Badge status={STATUS_BADGE[r.status ?? ""] ?? "info"}>{r.type}</Badge>
                </td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                  {r.release_date ? new Date(r.release_date).toLocaleDateString() : "—"}
                </td>
                {canWrite && (
                  <td className="px-4 py-3 flex gap-3">
                    <button
                      type="button"
                      onClick={() => setEditing(r)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      Edit
                    </button>
                    {canDelete && (
                      <button
                        type="button"
                        onClick={() => handleDelete(r.id)}
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
            <p className="font-semibold">{editing ? `Edit "${editing.title}"` : "New Release"}</p>
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
            {!editing && (
              <label className="flex flex-col gap-1 text-xs font-medium">
                Artist
                <select
                  name="artist_profile_id"
                  required
                  className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
                >
                  <option value="">Select…</option>
                  {artists.map((a) => (
                    <option key={a.id} value={a.id}>{a.artist_name}</option>
                  ))}
                </select>
              </label>
            )}
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
              Type
              <select
                name="type"
                defaultValue={editing?.type ?? "Single"}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              >
                {TYPES.map((t) => <option key={t} value={t}>{t}</option>)}
              </select>
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Release Date
              <input
                type="date"
                name="release_date"
                defaultValue={editing?.release_date?.slice(0, 10)}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Status
              <select
                name="status"
                defaultValue={editing?.status ?? "Draft"}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              >
                {STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
              </select>
            </label>
            <div className="flex items-end sm:col-span-2">
              <Button type="submit" disabled={submitting}>
                {submitting ? "Saving…" : editing ? "Save Changes" : "Add Release"}
              </Button>
            </div>
          </form>
          {error && <p className="mt-3 text-sm text-[var(--color-state-error)]">{error}</p>}
        </div>
      )}
    </div>
  );
}
