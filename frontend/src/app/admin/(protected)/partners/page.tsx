"use client";

import { Fragment, useState, useEffect } from "react";
import { authGet, authPatch, getStoredAuth } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";

type Partner = {
  id: number;
  organization_name: string;
  contact_person: string;
  email: string;
  type: string | null;
  message: string | null;
  status: string;
  created_at: string;
};

const STATUSES = ["New", "In Discussion", "Active"];
const STATUS_BADGE: Record<string, "success" | "warning" | "info"> = {
  Active: "success",
  "In Discussion": "warning",
  New: "info",
};

export default function AdminPartnersPage() {
  const [partners, setPartners] = useState<Partner[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [filter, setFilter] = useState("");
  const [expanded, setExpanded] = useState<number | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [savingId, setSavingId] = useState<number | null>(null);

  const role = getStoredAuth()?.user.role ?? null;
  const canWrite = role === "Super Administrator";

  function load(status: string) {
    document.title = "Partners · Tramax Admin";
    const path = status ? `admin/partners?status=${encodeURIComponent(status)}` : "admin/partners";
    authGet<Partner[]>(path).then((data) => {
      setPartners(data);
      setLoaded(true);
    });
  }

  useEffect(() => {
    load(filter);
  }, [filter]);

  async function handleStatusChange(id: number, status: string) {
    setSavingId(id);
    setError(null);
    const result = await authPatch(`admin/partners/${id}/status`, { status });
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

  if (partners === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Partners</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Partnerships (discovery.md §3: Super
          Administrator or Management only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Partners</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Enquiries from distributors, publishers, brands, and media companies. Only Super
        Administrator can change status — Management here has Read only.
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
              <th className="px-4 py-3 font-semibold">Organisation</th>
              <th className="px-4 py-3 font-semibold">Contact</th>
              <th className="px-4 py-3 font-semibold">Type</th>
              <th className="px-4 py-3 font-semibold">Received</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              <th className="px-4 py-3 font-semibold"></th>
            </tr>
          </thead>
          <tbody>
            {partners.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={6}>
                  No partner enquiries{filter ? ` with status "${filter}"` : ""}.
                </td>
              </tr>
            )}
            {partners.map((p) => (
              <Fragment key={p.id}>
                <tr className="border-b border-[var(--color-border-default)] last:border-0">
                  <td className="px-4 py-3">{p.organization_name}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">{p.contact_person}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">{p.type ?? "—"}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                    {new Date(p.created_at).toLocaleDateString()}
                  </td>
                  <td className="px-4 py-3">
                    <Badge status={STATUS_BADGE[p.status] ?? "info"}>{p.status}</Badge>
                  </td>
                  <td className="px-4 py-3">
                    <button
                      type="button"
                      onClick={() => setExpanded(expanded === p.id ? null : p.id)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      {expanded === p.id ? "Hide" : "Review"}
                    </button>
                  </td>
                </tr>
                {expanded === p.id && (
                  <tr className="border-b border-[var(--color-border-default)] bg-[var(--color-bg-raised)]">
                    <td colSpan={6} className="px-4 py-4">
                      <div className="grid gap-3 sm:grid-cols-2">
                        <p className="text-xs text-[var(--color-text-secondary)]">
                          <span className="font-medium text-[var(--color-text-primary)]">Email:</span> {p.email}
                        </p>
                        {p.message && (
                          <p className="text-xs text-[var(--color-text-secondary)] sm:col-span-2">
                            <span className="font-medium text-[var(--color-text-primary)]">Message:</span>{" "}
                            {p.message}
                          </p>
                        )}
                      </div>

                      {canWrite && (
                        <div className="mt-4 flex items-center gap-3">
                          <select
                            defaultValue={p.status}
                            onChange={(e) => handleStatusChange(p.id, e.target.value)}
                            disabled={savingId === p.id}
                            className="h-9 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
                          >
                            {STATUSES.map((s) => (
                              <option key={s} value={s}>{s}</option>
                            ))}
                          </select>
                          {savingId === p.id && (
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
