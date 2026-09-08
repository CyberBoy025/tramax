"use client";

import { Fragment, useEffect, useState, type FormEvent } from "react";
import { authGet, authPost, authDelete, getStoredAuth, publicGet } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

type LineItem = { id: number; source: string; amount: string };

type Statement = {
  id: number;
  period_start: string;
  period_end: string;
  total_revenue: string;
  company_share: string;
  artist_share: string;
  status: string;
  artist: { id: number; artist_name: string; slug: string };
  line_items?: LineItem[];
};

type ArtistOption = { id: number; artist_name: string };

const STATUSES = ["Draft", "Pending", "Paid"];
const SOURCES = ["Streaming", "Publishing", "Licensing", "Other"];
const STATUS_BADGE: Record<string, "success" | "warning" | "info"> = {
  Paid: "success",
  Pending: "warning",
  Draft: "info",
};

const money = (v: string) => `₦${Number(v).toLocaleString()}`;
const shortDate = (v: string) => new Date(v).toLocaleDateString();

export default function AdminRoyaltyPage() {
  const [statements, setStatements] = useState<Statement[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [artists, setArtists] = useState<ArtistOption[]>([]);
  const [lineItemRows, setLineItemRows] = useState(1);
  const [expanded, setExpanded] = useState<number | null>(null);
  const [expandedDetail, setExpandedDetail] = useState<Statement | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const role = getStoredAuth()?.user.role ?? null;
  const canDelete = role === "Super Administrator";
  const canWrite = role === "Super Administrator" || role === "Finance";

  async function load() {
    const data = await authGet<Statement[]>("admin/royalty-statements");
    setStatements(data);
    setLoaded(true);
  }

  useEffect(() => {
    document.title = "Royalty & Revenue · Tramax Admin";
    authGet<Statement[]>("admin/royalty-statements").then((data) => {
      setStatements(data);
      setLoaded(true);
    });
    publicGet<ArtistOption[]>("artists").then((a) => setArtists(a ?? []));
  }, []);

  async function toggleExpand(id: number) {
    if (expanded === id) {
      setExpanded(null);
      setExpandedDetail(null);
      return;
    }
    setExpanded(id);
    const detail = await authGet<Statement>(`admin/royalty-statements/${id}`);
    setExpandedDetail(detail);
  }

  async function handleCreate(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);

    const form = event.currentTarget;
    const raw = Object.fromEntries(new FormData(form)) as Record<string, string>;

    const line_items = Array.from({ length: lineItemRows })
      .map((_, i) => ({ source: raw[`li_source_${i}`], amount: raw[`li_amount_${i}`] }))
      .filter((li) => li.source && li.amount);

    const body = {
      artist_profile_id: raw.artist_profile_id,
      period_start: raw.period_start,
      period_end: raw.period_end,
      total_revenue: raw.total_revenue || "0",
      company_share: raw.company_share || "0",
      artist_share: raw.artist_share || "0",
      status: raw.status,
      ...(line_items.length > 0 ? { line_items } : {}),
    };

    const result = await authPost("admin/royalty-statements", body);
    setSubmitting(false);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    form.reset();
    setLineItemRows(1);
    load();
  }

  async function handleDelete(id: number) {
    const result = await authDelete(`admin/royalty-statements/${id}`);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    if (expanded === id) setExpanded(null);
    load();
  }

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (statements === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Royalty &amp; Revenue</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Royalty Management (discovery.md §3: Super
          Administrator, Management, A&amp;R, or Finance only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Royalty &amp; Revenue</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Statements and per-source breakdowns, entered directly — not calculated from streaming
        data (README.md §14).
      </p>

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Artist</th>
              <th className="px-4 py-3 font-semibold">Period</th>
              <th className="px-4 py-3 font-semibold">Revenue</th>
              <th className="px-4 py-3 font-semibold">Artist Share</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              <th className="px-4 py-3 font-semibold"></th>
            </tr>
          </thead>
          <tbody>
            {statements.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={6}>
                  No royalty statements yet.
                </td>
              </tr>
            )}
            {statements.map((s) => (
              <Fragment key={s.id}>
                <tr className="border-b border-[var(--color-border-default)] last:border-0">
                  <td className="px-4 py-3">{s.artist.artist_name}</td>
                  <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                    {shortDate(s.period_start)} – {shortDate(s.period_end)}
                  </td>
                  <td className="px-4 py-3 font-data">{money(s.total_revenue)}</td>
                  <td className="px-4 py-3 font-data">{money(s.artist_share)}</td>
                  <td className="px-4 py-3">
                    <Badge status={STATUS_BADGE[s.status] ?? "info"}>{s.status}</Badge>
                  </td>
                  <td className="px-4 py-3 flex gap-3">
                    <button
                      type="button"
                      onClick={() => toggleExpand(s.id)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      {expanded === s.id ? "Hide" : "Breakdown"}
                    </button>
                    {canDelete && (
                      <button
                        type="button"
                        onClick={() => handleDelete(s.id)}
                        className="text-xs text-[var(--color-state-error)] hover:underline"
                      >
                        Delete
                      </button>
                    )}
                  </td>
                </tr>
                {expanded === s.id && (
                  <tr className="border-b border-[var(--color-border-default)] bg-[var(--color-bg-raised)]">
                    <td colSpan={6} className="px-4 py-3">
                      {!expandedDetail ? (
                        <span className="text-xs text-[var(--color-text-muted)]">Loading breakdown…</span>
                      ) : expandedDetail.line_items && expandedDetail.line_items.length > 0 ? (
                        <div className="flex flex-wrap gap-4">
                          {expandedDetail.line_items.map((li) => (
                            <span key={li.id} className="text-xs text-[var(--color-text-secondary)]">
                              <span className="font-medium text-[var(--color-text-primary)]">{li.source}:</span>{" "}
                              {money(li.amount)}
                            </span>
                          ))}
                        </div>
                      ) : (
                        <span className="text-xs text-[var(--color-text-muted)]">No line items recorded.</span>
                      )}
                    </td>
                  </tr>
                )}
              </Fragment>
            ))}
          </tbody>
        </table>
      </div>

      {canWrite && (
      <div className="mt-[var(--space-xl)] rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
        <p className="font-semibold">New Royalty Statement</p>
        <form onSubmit={handleCreate} className="mt-[var(--space-md)] flex flex-col gap-[var(--space-md)]">
          <div className="grid gap-[var(--space-md)] sm:grid-cols-3">
            <label className="flex flex-col gap-1 text-xs font-medium">
              Artist
              <select name="artist_profile_id" required className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm">
                <option value="">Select…</option>
                {artists.map((a) => (
                  <option key={a.id} value={a.id}>{a.artist_name}</option>
                ))}
              </select>
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Period Start
              <input type="date" name="period_start" required className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Period End
              <input type="date" name="period_end" required className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Total Revenue (₦)
              <input type="number" step="0.01" name="total_revenue" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Company Share (₦)
              <input type="number" step="0.01" name="company_share" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Artist Share (₦)
              <input type="number" step="0.01" name="artist_share" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Status
              <select name="status" defaultValue="Draft" className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm">
                {STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
              </select>
            </label>
          </div>

          <div>
            <p className="text-xs font-medium text-[var(--color-text-secondary)]">Line items (optional)</p>
            <div className="mt-2 flex flex-col gap-2">
              {Array.from({ length: lineItemRows }).map((_, i) => (
                <div key={i} className="flex gap-2">
                  <select name={`li_source_${i}`} className="h-9 flex-1 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm">
                    <option value="">Source…</option>
                    {SOURCES.map((s) => <option key={s} value={s}>{s}</option>)}
                  </select>
                  <input type="number" step="0.01" name={`li_amount_${i}`} placeholder="Amount" className="h-9 w-32 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm" />
                </div>
              ))}
            </div>
            <button
              type="button"
              onClick={() => setLineItemRows((n) => n + 1)}
              className="mt-2 text-xs text-[var(--color-accent-primary)] hover:underline"
            >
              + Add another line item
            </button>
          </div>

          <Button type="submit" disabled={submitting} className="self-start">
            {submitting ? "Saving…" : "Add Statement"}
          </Button>
        </form>
        {error && <p className="mt-3 text-sm text-[var(--color-state-error)]">{error}</p>}
      </div>
      )}
    </div>
  );
}
