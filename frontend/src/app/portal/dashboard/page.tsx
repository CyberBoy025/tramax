import { Badge } from "@/components/ui/badge";

export const metadata = { title: "Artist Dashboard" };

export default function PortalDashboardPage() {
  return (
    <div>
      <h1 className="text-2xl font-semibold">Welcome back</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Sample data — replaced by GET /api/v1/me/* (discovery.md §4.2) once the backend exists.
      </p>

      <div className="mt-[var(--space-xl)] grid grid-cols-2 gap-[var(--space-lg)] sm:grid-cols-4">
        {[
          { label: "Releases", value: "0" },
          { label: "Tracks", value: "0" },
          { label: "Streams", value: "—" },
          { label: "Earnings", value: "—" },
        ].map((stat) => (
          <div
            key={stat.label}
            className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]"
          >
            <p className="font-data text-2xl font-semibold">{stat.value}</p>
            <p className="mt-1 text-xs text-[var(--color-text-secondary)]">{stat.label}</p>
          </div>
        ))}
      </div>

      <div className="mt-[var(--space-xl)] rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
        <div className="flex items-center justify-between">
          <p className="font-semibold">Latest Release</p>
          <Badge status="info">No releases yet</Badge>
        </div>
        <p className="mt-2 text-sm text-[var(--color-text-secondary)]">
          Submit your first release to see its status here.
        </p>
      </div>
    </div>
  );
}
