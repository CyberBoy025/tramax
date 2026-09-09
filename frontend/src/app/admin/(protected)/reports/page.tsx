"use client";

import { useEffect, useState } from "react";
import { authGet } from "@/lib/auth";

type Counts = Record<string, number>;
type CountSection = { total: number; by_status: Counts };
type ReleasesSection = { total: number; by_status: Counts; by_type: Counts };
type RightsSection = { total: number; by_copyright_status: Counts; by_licensing_status: Counts };
type RoyaltySection = {
  total: number;
  total_revenue: number;
  company_share: number;
  artist_share: number;
  by_status: Counts;
};
type UsersSection = { total: number; by_role: Counts };

type Summary = {
  scope: "full" | "artist_management" | "finance";
  generated_at: string;
  artists?: CountSection;
  applications?: CountSection;
  releases?: ReleasesSection;
  rights_records?: RightsSection;
  events?: CountSection;
  royalty?: RoyaltySection;
  licensing_requests?: CountSection;
  partners?: CountSection;
  products?: CountSection;
  news?: CountSection;
  users?: UsersSection;
};

const SCOPE_NOTE: Record<Summary["scope"], string> = {
  full: "Full summary across every module.",
  artist_management: "Scoped to Artist Management, Catalogue, Rights, and Events — your functional domain (discovery.md §3: A&R gets Read own scope).",
  finance: "Scoped to Royalty and Licensing — your functional domain (discovery.md §3: Finance gets Read own scope).",
};

function naira(value: number): string {
  return `₦${value.toLocaleString()}`;
}

function Breakdown({ label, data }: { label: string; data: Counts }) {
  const entries = Object.entries(data);
  if (entries.length === 0) return null;
  return (
    <div className="mt-3">
      <p className="text-xs font-medium text-[var(--color-text-secondary)]">{label}</p>
      <div className="mt-1 flex flex-wrap gap-2">
        {entries.map(([key, value]) => (
          <span
            key={key}
            className="rounded-[var(--radius-pill)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 py-1 text-xs"
          >
            {key}: <span className="font-data">{value}</span>
          </span>
        ))}
      </div>
    </div>
  );
}

function SectionCard({
  title,
  total,
  children,
}: {
  title: string;
  total: number | string;
  children?: React.ReactNode;
}) {
  return (
    <div className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
      <div className="flex items-baseline justify-between gap-4">
        <p className="font-semibold">{title}</p>
        <p className="font-data text-2xl font-semibold">{total}</p>
      </div>
      {children}
    </div>
  );
}

export default function AdminReportsPage() {
  const [summary, setSummary] = useState<Summary | null>(null);
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    document.title = "Reports & Analytics · Tramax Admin";
    authGet<Summary>("admin/reports/summary").then((data) => {
      setSummary(data);
      setLoaded(true);
    });
  }, []);

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (summary === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Reports &amp; Analytics</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Reports &amp; Analytics (discovery.md §3: Super
          Administrator, Management, A&amp;R, or Finance only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Reports &amp; Analytics</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        {SCOPE_NOTE[summary.scope]} Live counts and sums over existing records — a summary
        view, not a separate reporting system (README.md §4).
      </p>
      <p className="mt-1 text-xs text-[var(--color-text-muted)]">
        Generated {new Date(summary.generated_at).toLocaleString()}
      </p>

      <div className="mt-[var(--space-xl)] grid gap-[var(--space-lg)] sm:grid-cols-2 lg:grid-cols-3">
        {summary.artists && (
          <SectionCard title="Artists" total={summary.artists.total}>
            <Breakdown label="By status" data={summary.artists.by_status} />
          </SectionCard>
        )}

        {summary.applications && (
          <SectionCard title="Applications" total={summary.applications.total}>
            <Breakdown label="By status" data={summary.applications.by_status} />
          </SectionCard>
        )}

        {summary.releases && (
          <SectionCard title="Releases" total={summary.releases.total}>
            <Breakdown label="By status" data={summary.releases.by_status} />
            <Breakdown label="By type" data={summary.releases.by_type} />
          </SectionCard>
        )}

        {summary.rights_records && (
          <SectionCard title="Rights Records" total={summary.rights_records.total}>
            <Breakdown label="By copyright status" data={summary.rights_records.by_copyright_status} />
            <Breakdown label="By licensing status" data={summary.rights_records.by_licensing_status} />
          </SectionCard>
        )}

        {summary.events && (
          <SectionCard title="Events" total={summary.events.total}>
            <Breakdown label="By status" data={summary.events.by_status} />
          </SectionCard>
        )}

        {summary.royalty && (
          <SectionCard title="Royalty Statements" total={summary.royalty.total}>
            <div className="mt-3 grid grid-cols-3 gap-2 text-center">
              <div>
                <p className="font-data text-sm font-semibold">{naira(summary.royalty.total_revenue)}</p>
                <p className="text-xs text-[var(--color-text-muted)]">Total revenue</p>
              </div>
              <div>
                <p className="font-data text-sm font-semibold">{naira(summary.royalty.company_share)}</p>
                <p className="text-xs text-[var(--color-text-muted)]">Company share</p>
              </div>
              <div>
                <p className="font-data text-sm font-semibold">{naira(summary.royalty.artist_share)}</p>
                <p className="text-xs text-[var(--color-text-muted)]">Artist share</p>
              </div>
            </div>
            <Breakdown label="By status" data={summary.royalty.by_status} />
          </SectionCard>
        )}

        {summary.licensing_requests && (
          <SectionCard title="Licensing Requests" total={summary.licensing_requests.total}>
            <Breakdown label="By status" data={summary.licensing_requests.by_status} />
          </SectionCard>
        )}

        {summary.partners && (
          <SectionCard title="Partners" total={summary.partners.total}>
            <Breakdown label="By status" data={summary.partners.by_status} />
          </SectionCard>
        )}

        {summary.products && (
          <SectionCard title="Store Products" total={summary.products.total}>
            <Breakdown label="By status" data={summary.products.by_status} />
          </SectionCard>
        )}

        {summary.news && (
          <SectionCard title="News Posts" total={summary.news.total}>
            <Breakdown label="By status" data={summary.news.by_status} />
          </SectionCard>
        )}

        {summary.users && (
          <SectionCard title="Users" total={summary.users.total}>
            <Breakdown label="By role" data={summary.users.by_role} />
          </SectionCard>
        )}
      </div>
    </div>
  );
}
