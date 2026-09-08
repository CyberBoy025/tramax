"use client";

import { useEffect, useState } from "react";
import { Badge } from "@/components/ui/badge";
import { authGet } from "@/lib/auth";

type Application = {
  id: number;
  full_name: string;
  artist_name: string;
  genre: string | null;
  status: string;
};

const STATUS_TO_BADGE: Record<string, "success" | "warning" | "info" | "error"> = {
  Accepted: "success",
  Shortlisted: "info",
  "Under Review": "warning",
  Submitted: "warning",
  Rejected: "error",
};

export default function AdminDashboardPage() {
  const [applications, setApplications] = useState<Application[] | null>(null);

  useEffect(() => {
    document.title = "Admin Dashboard · Tramax Entertainment";
    authGet<Application[]>("admin/applications").then(setApplications);
  }, []);

  const pending = applications?.filter((a) => a.status === "Submitted" || a.status === "Under Review").length ?? 0;
  const accepted = applications?.filter((a) => a.status === "Accepted").length ?? 0;

  return (
    <div>
      <h1 className="text-2xl font-semibold">Operational Summary</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Live from GET /api/v1/admin/applications. Only Applications, Artists, and Releases have
        admin endpoints so far — the rest of this dashboard is still the Phase 2 wireframe.
      </p>

      <div className="mt-[var(--space-xl)] grid grid-cols-2 gap-[var(--space-lg)] sm:grid-cols-4">
        {[
          { label: "Pending Applications", value: pending },
          { label: "Accepted Applications", value: accepted },
          { label: "Open Licensing Requests", value: "—" },
          { label: "Upcoming Events", value: "—" },
        ].map((stat) => (
          <div
            key={stat.label}
            className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]"
          >
            <p className="font-data text-2xl font-semibold">
              {applications === null && typeof stat.value === "number" ? "…" : stat.value}
            </p>
            <p className="mt-1 text-xs text-[var(--color-text-secondary)]">{stat.label}</p>
          </div>
        ))}
      </div>

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Applicant</th>
              <th className="px-4 py-3 font-semibold">Artist Name</th>
              <th className="px-4 py-3 font-semibold">Genre</th>
              <th className="px-4 py-3 font-semibold">Status</th>
            </tr>
          </thead>
          <tbody>
            {applications === null && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={4}>
                  Loading…
                </td>
              </tr>
            )}
            {applications?.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={4}>
                  No applications yet.
                </td>
              </tr>
            )}
            {applications?.map((app) => (
              <tr key={app.id} className="border-b border-[var(--color-border-default)] last:border-0">
                <td className="px-4 py-3">{app.full_name}</td>
                <td className="px-4 py-3">{app.artist_name}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{app.genre ?? "—"}</td>
                <td className="px-4 py-3">
                  <Badge status={STATUS_TO_BADGE[app.status] ?? "info"}>{app.status}</Badge>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
