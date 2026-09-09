"use client";

import { useEffect, useState } from "react";
import { authGet } from "@/lib/auth";

type Counts = Record<string, number>;
type CountSection = { total: number; by_status: Counts };
type RoyaltySection = { total: number; total_revenue: number; artist_share: number; by_status: Counts };
type Summary = {
  scope: "full" | "artist_management" | "finance";
  artists?: CountSection;
  applications?: CountSection;
  releases?: CountSection;
  events?: CountSection;
  royalty?: RoyaltySection;
  licensing_requests?: CountSection;
  users?: { total: number };
};

type Product = { status: string };
type NewsPost = { status: string };

type Stat = { label: string; value: string | number };

const SCOPE_LABEL: Record<Summary["scope"], string> = {
  full: "Live from GET /api/v1/admin/reports/summary.",
  artist_management: "Scoped to your domain (Artist Management, Catalogue, Events) — GET /api/v1/admin/reports/summary.",
  finance: "Scoped to your domain (Royalty, Licensing) — GET /api/v1/admin/reports/summary.",
};

function naira(value: number): string {
  return `₦${value.toLocaleString()}`;
}

function buildStatsFromSummary(summary: Summary): Stat[] {
  const stats: Stat[] = [];
  if (summary.applications) {
    const byStatus = summary.applications.by_status;
    const pending = (byStatus["Submitted"] ?? 0) + (byStatus["Under Review"] ?? 0);
    stats.push({ label: "Pending Applications", value: pending });
  }
  if (summary.artists) stats.push({ label: "Artists", value: summary.artists.total });
  if (summary.releases) stats.push({ label: "Releases", value: summary.releases.total });
  if (summary.events) {
    stats.push({ label: "Upcoming Events", value: summary.events.by_status["Upcoming"] ?? 0 });
  }
  if (summary.licensing_requests) {
    const open = summary.licensing_requests.total - (summary.licensing_requests.by_status["Declined"] ?? 0);
    stats.push({ label: "Open Licensing Requests", value: open });
  }
  if (summary.royalty) stats.push({ label: "Total Royalty Revenue", value: naira(summary.royalty.total_revenue) });
  if (summary.users) stats.push({ label: "Users", value: summary.users.total });
  return stats.slice(0, 4);
}

export default function AdminDashboardPage() {
  const [stats, setStats] = useState<Stat[] | null>(null);
  const [note, setNote] = useState("");
  const [loaded, setLoaded] = useState(false);

  useEffect(() => {
    document.title = "Admin Dashboard · Tramax Entertainment";

    authGet<Summary>("admin/reports/summary").then(async (summary) => {
      if (summary) {
        setNote(SCOPE_LABEL[summary.scope]);
        setStats(buildStatsFromSummary(summary));
        setLoaded(true);
        return;
      }

      // Content Manager has no Reports access (discovery.md §3) — fall
      // back to their own two modules directly instead of showing nothing.
      const [products, news] = await Promise.all([
        authGet<Product[]>("admin/products"),
        authGet<NewsPost[]>("admin/news"),
      ]);
      if (products || news) {
        setNote("Scoped to your domain (Store, Content) — GET /api/v1/admin/products and /admin/news.");
        setStats([
          { label: "Published Products", value: products?.filter((p) => p.status === "Published").length ?? 0 },
          { label: "Draft Products", value: products?.filter((p) => p.status === "Draft").length ?? 0 },
          { label: "Published News", value: news?.filter((n) => n.status === "Published").length ?? 0 },
          { label: "Draft News", value: news?.filter((n) => n.status === "Draft").length ?? 0 },
        ]);
      }
      setLoaded(true);
    });
  }, []);

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Operational Summary</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        {note || "Your role doesn't have summary data configured for this dashboard yet."}
      </p>

      {stats && stats.length > 0 && (
        <div className="mt-[var(--space-xl)] grid grid-cols-2 gap-[var(--space-lg)] sm:grid-cols-4">
          {stats.map((stat) => (
            <div
              key={stat.label}
              className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]"
            >
              <p className="font-data text-2xl font-semibold">{stat.value}</p>
              <p className="mt-1 text-xs text-[var(--color-text-secondary)]">{stat.label}</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
