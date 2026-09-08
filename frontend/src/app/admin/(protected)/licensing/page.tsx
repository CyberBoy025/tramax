"use client";

import { Fragment, useState, useEffect } from "react";
import { authGet, authPatch, getStoredAuth } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";

type LicensingRequest = {
  id: number;
  company_name: string;
  contact_person: string;
  email: string;
  music_required: string | null;
  project_type: string | null;
  usage: string | null;
  duration: string | null;
  territory: string | null;
  budget: string | null;
  message: string | null;
  status: string;
  release: { id: number; title: string; slug: string } | null;
  created_at: string;
};

const STATUSES = ["New", "In Review", "Approved", "Declined"];
const STATUS_BADGE: Record<string, "success" | "warning" | "info" | "error"> = {
  Approved: "success",
  "In Review": "warning",
  New: "info",
  Declined: "error",
};

export default function AdminLicensingPage() {
  const [requests, setRequests] = useState<LicensingRequest[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [filter, setFilter] = useState("");
  const [expanded, setExpanded] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [savingId, setSavingId] = useState<number | null>(null);

  const role = getStoredAuth()?.user.role ?? null;
  const canWrite = role === "Super Administrator";

  function load(status: string) {
    document.title = "Licensing Requests · Tramax Admin";
    const path = status
      ? `admin/licensing-requests?status=${encodeURIComponent(status)}`
      : "admin/licensing-requests";
    authGet<LicensingRequest[]>(path).then((data) => {
      setRequests(data);
      setLoaded(true);
    });
  }

  useEffect(() => {
    load(filter);
  }, [filter]);

  async function handleStatusChange(id: number, status: string) {
    setSavingId(id);
    setError(null);
    const result = await authPatch(`admin/licensing-requests/${id}/status`, { status });
    setSavingId(null);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    load(filter);
  }

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (requests === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Licensing Requests</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Licensing Requests (discovery.md §3: Super
          Administrator, Management, A&amp;R, or Finance only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Licensing Requests</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Inbound requests from filmmakers, advertisers, and media companies. Only Super
        Administrator can change status — everyone else here has Read only.
      </p>

      <div className="mt-[var(--space-lg)] flex flex-wrap gap-2">
        <button
          type="button"
          onClick={() => setFilter("")}
          className={`rounded-[var(--radius-pill)] border px-3 py-1 text-xs ${filter === "" ? "border-[var(--color-accent-primary)] text-[var(--color-accent-primary)]" : "border-[var(--color-border-default)] text-[var(--color-text-secondary)]"}`}
        >
          All
        </button>
        {STATUSES.map((s) => (
          <button
            key={s}
            type="button"
            onClick={() => setFilter(s)}
            className={`rounded-[var(--radius-pill)] border px-3 py-1 text-xs ${filter === s ? "border-[var(--color-accent-primary)] text-[var(--color-accent-primary)]" : "border-[var(--color-border-default)] text-[var(--color-text-secondary)]"}`}
          >
            {s}
          </button>
        ))}
      </div>

      {error && <p className="mt-3 text-sm text-[var(--color-state-error)]">{error}</p>}

      <div className="mt-[var(--space-lg)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Company</th>
              <th className="px-4 py-3 font-semibold">Contact</th>
              <th className="px-4 py-3 font-semibold">Project Type</th>
              <th className="px-4 py-3 font-semibold">Received</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              <th className="px-4 py-3 font-semibold"></th>
            </tr>
          </thead>
          <tbody>
            {requests.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={6}>
                  No licensing requests{filter ? ` with status "${filter}"` : ""}.
                </td>
              </tr>
            )}
            {requests.map((r) => (
              <Fragment key={r.id}>
                <tr className="border-b border-[var(--color-border-default)] last:border-0">
                  <td className="px-4 py-3">{r.company_name}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">{r.contact_person}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">{r.project_type ?? "—"}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                    {new Date(r.created_at).toLocaleDateString()}
                  </td>
                  <td className="px-4 py-3">
                    <Badge status={STATUS_BADGE[r.status] ?? "info"}>{r.status}</Badge>
                  </td>
                  <td className="px-4 py-3">
                    <button
                      type="button"
                      onClick={() => setExpanded(expanded === r.id ? null : r.id)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      {expanded === r.id ? "Hide" : "Review"}
                    </button>
                  </td>
                </tr>
                {expanded === r.id && (
                  <tr className="border-b border-[var(--color-border-default)] bg-[var(--color-bg-raised)]">
                    <td colSpan={6} className="px-4 py-4">
                      <div className="grid gap-3 sm:grid-cols-2">
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Email:</span> {r.email}
                        </p>
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Music Required:</span>{" "}
                          {r.music_required ?? "—"}
                        </p>
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Usage:</span> {r.usage ?? "—"}
                        </p>
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Duration:</span>{" "}
                          {r.duration ?? "—"}
                        </p>
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Territory:</span>{" "}
                          {r.territory ?? "—"}
                        </p>
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Budget:</span>{" "}
                          {r.budget ?? "—"}
                        </p>
                        {r.release && (
                          <p className="text-xs text-[var(--color-text-secondary)]">
                            <span className="font-medium text-[var(--color-text-primary)]">Related Release:</span>{" "}
                            {r.release.title}
                          </p>
                        )}
                        {r.message && (
                          <p className="text-xs text-[var(--color-text-secondary)] sm:col-span-2">
                            <span className="font-medium text-[var(--color-text-primary)]">Message:</span>{" "}
                            {r.message}
                          </p>
                        )}
                      </div>

                      {canWrite && (
                        <div className="mt-4 flex items-center gap-3">
                          <select
                            defaultValue={r.status}
                            onChange={(e) => handleStatusChange(r.id, e.target.value)}
                            disabled={savingId === r.id}
                            className="h-9 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
                          >
                            {STATUSES.map((s) => (
                              <option key={s} value={s}>{s}</option>
                            ))}
                          </select>
                          {savingId === r.id && (
                            <span className="text-xs text-[var(--color-text-muted)]">Saving…</span>
                          )}
                        </div>
                      )}
                    </td>
                  </tr>
                )}
              </Fragment>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
