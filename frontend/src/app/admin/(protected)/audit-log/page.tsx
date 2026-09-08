"use client";

import { Fragment, useEffect, useState } from "react";
import { authGet } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";

type AuditLogEntry = {
  id: number;
  user_id: number | null;
  action: string;
  entity_type: string;
  entity_id: number | null;
  metadata: Record<string, unknown> | null;
  created_at: string;
  user: { id: number; name: string; email: string } | null;
};

const ACTIONS = ["created", "updated", "deleted"];
const ACTION_BADGE: Record<string, "success" | "info" | "error"> = {
  created: "success",
  updated: "info",
  deleted: "error",
};
const ENTITY_TYPES = [
  "ArtistProfile",
  "ArtistApplication",
  "Release",
  "Event",
  "NewsPost",
  "Product",
  "RightsRecord",
  "RoyaltyStatement",
  "LicensingRequest",
  "Partner",
  "User",
];
const ENTITY_LABEL: Record<string, string> = {
  ArtistProfile: "Artist",
  ArtistApplication: "Application",
  Release: "Release",
  Event: "Event",
  NewsPost: "News Post",
  Product: "Product",
  RightsRecord: "Rights Record",
  RoyaltyStatement: "Royalty Statement",
  LicensingRequest: "Licensing Request",
  Partner: "Partner",
  User: "User",
};

export default function AdminAuditLogPage() {
  const [entries, setEntries] = useState<AuditLogEntry[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [actionFilter, setActionFilter] = useState("");
  const [entityFilter, setEntityFilter] = useState("");
  const [expanded, setExpanded] = useState<number | null>(null);

  function load(action: string, entityType: string) {
    document.title = "Audit Log · Tramax Admin";
    const params = new URLSearchParams();
    if (action) params.set("action", action);
    if (entityType) params.set("entity_type", entityType);
    const query = params.toString();
    authGet<AuditLogEntry[]>(`admin/audit-log${query ? `?${query}` : ""}`).then((data) => {
      setEntries(data);
      setLoaded(true);
    });
  }

  useEffect(() => {
    load(actionFilter, entityFilter);
  }, [actionFilter, entityFilter]);

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (entries === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Audit Log</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to the Audit Log (discovery.md §3: Super
          Administrator or Management only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Audit Log</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Read-only trail of administrative writes — every create, edit, and delete an admin makes
        through this dashboard. Public-site submissions (applications, licensing requests,
        partner enquiries) aren&apos;t logged here since no admin performed them.
      </p>

      <div className="mt-[var(--space-lg)] flex flex-wrap items-center gap-4">
        <div className="flex flex-wrap gap-2">
          <button
            type="button"
            onClick={() => setActionFilter("")}
            className={`rounded-[var(--radius-pill)] border px-3 py-1 text-xs ${actionFilter === "" ? "border-[var(--color-accent-primary)] text-[var(--color-accent-primary)]" : "border-[var(--color-border-default)] text-[var(--color-text-secondary)]"}`}
          >
            All actions
          </button>
          {ACTIONS.map((a) => (
            <button
              key={a}
              type="button"
              onClick={() => setActionFilter(a)}
              className={`rounded-[var(--radius-pill)] border px-3 py-1 text-xs capitalize ${actionFilter === a ? "border-[var(--color-accent-primary)] text-[var(--color-accent-primary)]" : "border-[var(--color-border-default)] text-[var(--color-text-secondary)]"}`}
            >
              {a}
            </button>
          ))}
        </div>

        <select
          value={entityFilter}
          onChange={(e) => setEntityFilter(e.target.value)}
          className="h-9 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-xs"
        >
          <option value="">All entity types</option>
          {ENTITY_TYPES.map((t) => (
            <option key={t} value={t}>{ENTITY_LABEL[t]}</option>
          ))}
        </select>
      </div>

      <div className="mt-[var(--space-lg)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">When</th>
              <th className="px-4 py-3 font-semibold">Admin</th>
              <th className="px-4 py-3 font-semibold">Action</th>
              <th className="px-4 py-3 font-semibold">Entity</th>
              <th className="px-4 py-3 font-semibold"></th>
            </tr>
          </thead>
          <tbody>
            {entries.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={5}>
                  No matching audit entries.
                </td>
              </tr>
            )}
            {entries.map((e) => (
              <Fragment key={e.id}>
                <tr className="border-b border-[var(--color-border-default)] last:border-0">
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                    {new Date(e.created_at).toLocaleString()}
                  </td>
                  <td className="px-4 py-3">{e.user?.name ?? "—"}</td>
                  <td className="px-4 py-3">
                    <Badge status={ACTION_BADGE[e.action] ?? "info"}>{e.action}</Badge>
                  </td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                    {ENTITY_LABEL[e.entity_type] ?? e.entity_type}
                    {e.entity_id != null && <span className="text-[var(--color-text-muted)]"> #{e.entity_id}</span>}
                  </td>
                  <td className="px-4 py-3">
                    {e.metadata && Object.keys(e.metadata).length > 0 && (
                      <button
                        type="button"
                        onClick={() => setExpanded(expanded === e.id ? null : e.id)}
                        className="text-xs text-[var(--color-accent-primary)] hover:underline"
                      >
                        {expanded === e.id ? "Hide" : "Details"}
                      </button>
                    )}
                  </td>
                </tr>
                {expanded === e.id && e.metadata && (
                  <tr className="border-b border-[var(--color-border-default)] bg-[var(--color-bg-raised)]">
                    <td colSpan={5} className="px-4 py-4">
                      <div className="grid gap-2 sm:grid-cols-2">
                        {Object.entries(e.metadata).map(([key, value]) => (
                          <p key={key} className="text-xs text-[var(--color-text-secondary)]">
                            <span className="font-medium text-[var(--color-text-primary)]">{key}:</span>{" "}
                            {value === null ? "—" : typeof value === "object" ? JSON.stringify(value) : String(value)}
                          </p>
                        ))}
                      </div>
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
