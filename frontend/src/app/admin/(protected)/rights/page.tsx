"use client";

import { useEffect, useState, type FormEvent } from "react";
import { authGet, authPost, authDelete, getStoredAuth, publicGet } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

type RightsRecord = {
  id: number;
  master_owner: string | null;
  publishing_owner: string | null;
  songwriter: string | null;
  producer: string | null;
  copyright_status: string;
  licensing_status: string;
  release: { id: number; title: string; slug: string } | null;
};

type ReleaseOption = { id: number; title: string; slug: string };

const COPYRIGHT_STATUSES = ["Active", "Disputed", "Expired"];
const LICENSING_STATUSES = ["Unlicensed", "Licensed", "Exclusive"];

const COPYRIGHT_BADGE: Record<string, "success" | "warning" | "error"> = {
  Active: "success",
  Disputed: "error",
  Expired: "warning",
};

export default function AdminRightsPage() {
  const [records, setRecords] = useState<RightsRecord[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [releases, setReleases] = useState<ReleaseOption[]>([]);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const role = getStoredAuth()?.user.role ?? null;
  const canDelete = role === "Super Administrator";
  const canWrite = role === "Super Administrator" || role === "A&R / Artist Manager";

  async function load() {
    const data = await authGet<RightsRecord[]>("admin/rights-records");
    setRecords(data);
    setLoaded(true);
  }

  useEffect(() => {
    document.title = "Rights & Catalogue · Tramax Admin";
    authGet<RightsRecord[]>("admin/rights-records").then((data) => {
      setRecords(data);
      setLoaded(true);
    });
    publicGet<ReleaseOption[]>("releases").then((r) => setReleases(r ?? []));
  }, []);

  async function handleCreate(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);

    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form));
    const body = Object.fromEntries(Object.entries(data).filter(([, v]) => v !== ""));

    const result = await authPost("admin/rights-records", body);
    setSubmitting(false);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    form.reset();
    load();
  }

  async function handleDelete(id: number) {
    const result = await authDelete(`admin/rights-records/${id}`);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    load();
  }

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (records === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Rights &amp; Catalogue</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Rights Management (discovery.md §3: Super
          Administrator, Management, A&amp;R, or Finance only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Rights &amp; Catalogue</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Ownership records — master, publishing, songwriter, producer, copyright and licensing
        status. Records only, no contract automation (README.md §14).
      </p>

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Release</th>
              <th className="px-4 py-3 font-semibold">Master Owner</th>
              <th className="px-4 py-3 font-semibold">Publishing Owner</th>
              <th className="px-4 py-3 font-semibold">Copyright</th>
              <th className="px-4 py-3 font-semibold">Licensing</th>
              {canDelete && <th className="px-4 py-3 font-semibold"></th>}
            </tr>
          </thead>
          <tbody>
            {records.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={canDelete ? 6 : 5}>
                  No rights records yet.
                </td>
              </tr>
            )}
            {records.map((r) => (
              <tr key={r.id} className="border-b border-[var(--color-border-default)] last:border-0">
                <td className="px-4 py-3">{r.release?.title ?? "—"}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{r.master_owner ?? "—"}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{r.publishing_owner ?? "—"}</td>
                <td className="px-4 py-3">
                  <Badge status={COPYRIGHT_BADGE[r.copyright_status] ?? "info"}>{r.copyright_status}</Badge>
                </td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{r.licensing_status}</td>
                {canDelete && (
                  <td className="px-4 py-3">
                    <button
                      type="button"
                      onClick={() => handleDelete(r.id)}
                      className="text-xs text-[var(--color-state-error)] hover:underline"
                    >
                      Delete
                    </button>
                  </td>
                )}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {canWrite && (
      <div className="mt-[var(--space-xl)] rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
        <p className="font-semibold">New Rights Record</p>
        <form onSubmit={handleCreate} className="mt-[var(--space-md)] grid gap-[var(--space-md)] sm:grid-cols-2">
          <label className="flex flex-col gap-1 text-xs font-medium">
            Release
            <select
              name="release_id"
              required
              className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
            >
              <option value="">Select a release…</option>
              {releases.map((r) => (
                <option key={r.id} value={r.id}>
                  {r.title}
                </option>
              ))}
            </select>
          </label>
          <label className="flex flex-col gap-1 text-xs font-medium">
            Master Owner
            <input name="master_owner" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
          </label>
          <label className="flex flex-col gap-1 text-xs font-medium">
            Publishing Owner
            <input name="publishing_owner" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
          </label>
          <label className="flex flex-col gap-1 text-xs font-medium">
            Songwriter
            <input name="songwriter" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
          </label>
          <label className="flex flex-col gap-1 text-xs font-medium">
            Producer
            <input name="producer" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
          </label>
          <label className="flex flex-col gap-1 text-xs font-medium">
            Copyright Status
            <select name="copyright_status" defaultValue="Active" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm">
              {COPYRIGHT_STATUSES.map((s) => (
                <option key={s} value={s}>{s}</option>
              ))}
            </select>
          </label>
          <label className="flex flex-col gap-1 text-xs font-medium">
            Licensing Status
            <select name="licensing_status" defaultValue="Unlicensed" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm">
              {LICENSING_STATUSES.map((s) => (
                <option key={s} value={s}>{s}</option>
              ))}
            </select>
          </label>
          <div className="flex items-end">
            <Button type="submit" disabled={submitting}>
              {submitting ? "Saving…" : "Add Rights Record"}
            </Button>
          </div>
        </form>
        {error && <p className="mt-3 text-sm text-[var(--color-state-error)]">{error}</p>}
      </div>
      )}
    </div>
  );
}
