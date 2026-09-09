"use client";

import { Fragment, useEffect, useState } from "react";
import { authGet } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";

type LineItem = { id: number; source: string; amount: string | number };
type Statement = {
  id: number;
  period_start: string;
  period_end: string;
  total_revenue: string | number;
  company_share: string | number;
  artist_share: string | number;
  status: string;
  line_items: LineItem[];
};

const STATUS_BADGE: Record<string, "success" | "warning" | "info"> = {
  Paid: "success",
  Pending: "warning",
  Draft: "info",
};

function naira(value: string | number): string {
  return `₦${Number(value).toLocaleString()}`;
}

export default function PortalRoyaltyPage() {
  const [statements, setStatements] = useState<Statement[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [expanded, setExpanded] = useState<number | null>(null);

  useEffect(() => {
    document.title = "Royalty & Earnings · Tramax Portal";
    authGet<Statement[]>("portal/royalty-statements").then((data) => {
      setStatements(data);
      setLoaded(true);
    });
  }, []);

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (statements === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Royalty &amp; Earnings</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your account isn&apos;t linked to a public artist profile yet.
        </p>
      </div>
    );
  }

  const totalEarnings = statements.reduce((sum, s) => sum + Number(s.artist_share), 0);
  const totalPaid = statements
    .filter((s) => s.status === "Paid")
    .reduce((sum, s) => sum + Number(s.artist_share), 0);

  return (
    <div>
      <h1 className="text-2xl font-semibold">Royalty &amp; Earnings</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Read-only — statements are entered by Finance (discovery.md §3: Royalty Management is
        Own (read) for artists).
      </p>

      <div className="mt-[var(--space-lg)] grid grid-cols-2 gap-[var(--space-lg)] sm:grid-cols-3">
        <div className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
          <p className="font-data text-2xl font-semibold">{naira(totalEarnings)}</p>
          <p className="mt-1 text-xs text-[var(--color-text-secondary)]">Total earnings (all statements)</p>
        </div>
        <div className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
          <p className="font-data text-2xl font-semibold">{naira(totalPaid)}</p>
          <p className="mt-1 text-xs text-[var(--color-text-secondary)]">Paid out</p>
        </div>
        <div className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
          <p className="font-data text-2xl font-semibold">{statements.length}</p>
          <p className="mt-1 text-xs text-[var(--color-text-secondary)]">Statements</p>
        </div>
      </div>

      <div className="mt-[var(--space-lg)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Period</th>
              <th className="px-4 py-3 font-semibold">Total Revenue</th>
              <th className="px-4 py-3 font-semibold">Your Share</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              <th className="px-4 py-3 font-semibold"></th>
            </tr>
          </thead>
          <tbody>
            {statements.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={5}>
                  No royalty statements yet.
                </td>
              </tr>
            )}
            {statements.map((s) => (
              <Fragment key={s.id}>
                <tr className="border-b border-[var(--color-border-default)] last:border-0">
                  <td className="px-4 py-3">
                    {new Date(s.period_start).toLocaleDateString()} – {new Date(s.period_end).toLocaleDateString()}
                  </td>
                  <td className="px-4 py-3 font-data text-[var(--color-text-secondary)]">{naira(s.total_revenue)}</td>
                  <td className="px-4 py-3 font-data">{naira(s.artist_share)}</td>
                  <td className="px-4 py-3">
                    <Badge status={STATUS_BADGE[s.status] ?? "info"}>{s.status}</Badge>
                  </td>
                  <td className="px-4 py-3">
                    {s.line_items.length > 0 && (
                      <button
                        type="button"
                        onClick={() => setExpanded(expanded === s.id ? null : s.id)}
                        className="text-xs text-[var(--color-accent-primary)] hover:underline"
                      >
                        {expanded === s.id ? "Hide" : "Breakdown"}
                      </button>
                    )}
                  </td>
                </tr>
                {expanded === s.id && (
                  <tr className="border-b border-[var(--color-border-default)] bg-[var(--color-bg-raised)]">
                    <td colSpan={5} className="px-4 py-4">
                      <div className="grid gap-2 sm:grid-cols-3">
                        {s.line_items.map((li) => (
                          <p key={li.id} className="text-xs text-[var(--color-text-secondary)]">
                            <span className="font-medium text-[var(--color-text-primary)]">{li.source}:</span>{" "}
                            {naira(li.amount)}
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
