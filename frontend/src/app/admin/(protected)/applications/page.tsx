"use client";

import { Fragment, useState, useEffect } from "react";
import { authGet, authPatch, getStoredAuth } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";

type Application = {
  id: number;
  full_name: string;
  artist_name: string;
  phone: string | null;
  email: string;
  location: string | null;
  genre: string | null;
  years_active: number | null;
  biography: string | null;
  demo_file_url: string | null;
  status: string;
  submitted_at: string;
};

const STATUSES = ["Submitted", "Under Review", "Shortlisted", "Accepted", "Rejected"];
const STATUS_BADGE: Record<string, "success" | "warning" | "info" | "error"> = {
  Accepted: "success",
  Shortlisted: "info",
  "Under Review": "warning",
  Submitted: "warning",
  Rejected: "error",
};

export default function AdminApplicationsPage() {
  const [applications, setApplications] = useState<Application[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [filter, setFilter] = useState("");
  const [expanded, setExpanded] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [savingId, setSavingId] = useState<number | null>(null);

  const role = getStoredAuth()?.user.role ?? null;
  const canWrite = role === "Super Administrator" || role === "A&R / Artist Manager";

  function load(status: string) {
    document.title = "Applications · Tramax Admin";
    const path = status ? `admin/applications?status=${encodeURIComponent(status)}` : "admin/applications";
    authGet<Application[]>(path).then((data) => {
      setApplications(data);
      setLoaded(true);
    });
  }

  useEffect(() => {
    load(filter);
  }, [filter]);

  async function handleStatusChange(id: number, status: string) {
    setSavingId(id);
    setError(null);
    const result = await authPatch(`admin/applications/${id}/status`, { status });
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

  if (applications === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Applications</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Artist Management (discovery.md §3: Super
          Administrator, Management, or A&amp;R only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Applications</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Submitted → Under Review → Shortlisted → Accepted (or Rejected). Accepting an
        application creates the artist&apos;s public profile automatically.
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
              <th className="px-4 py-3 font-semibold">Applicant</th>
              <th className="px-4 py-3 font-semibold">Artist Name</th>
              <th className="px-4 py-3 font-semibold">Genre</th>
              <th className="px-4 py-3 font-semibold">Submitted</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              <th className="px-4 py-3 font-semibold"></th>
            </tr>
          </thead>
          <tbody>
            {applications.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={6}>
                  No applications{filter ? ` with status "${filter}"` : ""}.
                </td>
              </tr>
            )}
            {applications.map((app) => (
              <Fragment key={app.id}>
                <tr className="border-b border-[var(--color-border-default)] last:border-0">
                  <td className="px-4 py-3">{app.full_name}</td>
                  <td className="px-4 py-3">{app.artist_name}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">{app.genre ?? "—"}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                    {new Date(app.submitted_at).toLocaleDateString()}
                  </td>
                  <td className="px-4 py-3">
                    <Badge status={STATUS_BADGE[app.status] ?? "info"}>{app.status}</Badge>
                  </td>
                  <td className="px-4 py-3">
                    <button
                      type="button"
                      onClick={() => setExpanded(expanded === app.id ? null : app.id)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      {expanded === app.id ? "Hide" : "Review"}
                    </button>
                  </td>
                </tr>
                {expanded === app.id && (
                  <tr className="border-b border-[var(--color-border-default)] bg-[var(--color-bg-raised)]">
                    <td colSpan={6} className="px-4 py-4">
                      <div className="grid gap-3 sm:grid-cols-2">
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Email:</span> {app.email}
                        </p>
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Phone:</span> {app.phone ?? "—"}
                        </p>
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Location:</span> {app.location ?? "—"}
                        </p>
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Years Active:</span>{" "}
                          {app.years_active ?? "—"}
                        </p>
                        {app.demo_file_url && (
                          <p className="text-xs text-[var(--color-text-secondary)] sm:col-span-2">
                            <span className="font-medium text-[var(--color-text-primary)]">Demo:</span>{" "}
                            <a href={app.demo_file_url} className="text-[var(--color-accent-primary)] underline">
                              {app.demo_file_url}
                            </a>
                          </p>
                        )}
                        {app.biography && (
                          <p className="text-xs text-[var(--color-text-secondary)] sm:col-span-2">
                            <span className="font-medium text-[var(--color-text-primary)]">Biography:</span>{" "}
                            {app.biography}
                          </p>
                        )}
                      </div>

                      {canWrite && (
                        <div className="mt-4 flex items-center gap-3">
                          <select
                            defaultValue={app.status}
                            onChange={(e) => handleStatusChange(app.id, e.target.value)}
                            disabled={savingId === app.id}
                            className="h-9 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
                          >
                            {STATUSES.map((s) => (
                              <option key={s} value={s}>{s}</option>
                            ))}
                          </select>
                          {savingId === app.id && (
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
