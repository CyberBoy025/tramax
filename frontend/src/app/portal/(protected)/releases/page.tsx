"use client";

import { useEffect, useState, type FormEvent } from "react";
import { authGet, authPost } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

type Release = {
  id: number;
  title: string;
  type: string;
  status: string;
  release_date: string | null;
  cover_art_url: string | null;
};

const TYPES = ["Single", "EP", "Album"];
const STATUS_BADGE: Record<string, "success" | "warning" | "info"> = {
  Published: "success",
  Processing: "warning",
  Draft: "info",
};

export default function PortalReleasesPage() {
  const [releases, setReleases] = useState<Release[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);
  const [showForm, setShowForm] = useState(false);

  function load() {
    document.title = "My Releases · Tramax Portal";
    authGet<Release[]>("portal/releases").then((data) => {
      setReleases(data);
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

    const result = await authPost("portal/releases", body);
    setSubmitting(false);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    form.reset();
    setShowForm(false);
    load();
  }

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (releases === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">My Releases</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your account isn&apos;t linked to a public artist profile yet.
        </p>
      </div>
    );
  }

  return (
    <div>
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-semibold">My Releases</h1>
        <Button type="button" onClick={() => setShowForm((v) => !v)}>
          {showForm ? "Cancel" : "Submit a Release"}
        </Button>
      </div>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Submitted releases come in as Draft — an A&amp;R Manager or Super Admin reviews and
        publishes from there. You can&apos;t publish directly.
      </p>

      {showForm && (
        <form onSubmit={handleSubmit} className="mt-[var(--space-lg)] grid max-w-2xl gap-[var(--space-md)] rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)] sm:grid-cols-2">
          <label className="flex flex-col gap-1 text-xs font-medium">
            Title
            <input
              name="title"
              required
              className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
            />
          </label>
          <label className="flex flex-col gap-1 text-xs font-medium">
            Type
            <select
              name="type"
              required
              defaultValue="Single"
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
              className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
            />
          </label>
          <label className="flex flex-col gap-1 text-xs font-medium">
            Cover Art URL
            <input
              name="cover_art_url"
              className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
            />
          </label>
          <div className="sm:col-span-2">
            <Button type="submit" disabled={submitting}>
              {submitting ? "Submitting…" : "Submit for Review"}
            </Button>
          </div>
          {error && <p className="text-sm text-[var(--color-state-error)] sm:col-span-2">{error}</p>}
        </form>
      )}

      <div className="mt-[var(--space-lg)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Title</th>
              <th className="px-4 py-3 font-semibold">Type</th>
              <th className="px-4 py-3 font-semibold">Release Date</th>
              <th className="px-4 py-3 font-semibold">Status</th>
            </tr>
          </thead>
          <tbody>
            {releases.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={4}>
                  No releases yet.
                </td>
              </tr>
            )}
            {releases.map((r) => (
              <tr key={r.id} className="border-b border-[var(--color-border-default)] last:border-0">
                <td className="px-4 py-3">{r.title}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{r.type}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                  {r.release_date ? new Date(r.release_date).toLocaleDateString() : "—"}
                </td>
                <td className="px-4 py-3">
                  <Badge status={STATUS_BADGE[r.status] ?? "info"}>{r.status}</Badge>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
