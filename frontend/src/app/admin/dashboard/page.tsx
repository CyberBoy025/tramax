import { Badge } from "@/components/ui/badge";

export const metadata = { title: "Admin Dashboard" };

export default function AdminDashboardPage() {
  return (
    <div>
      <h1 className="text-2xl font-semibold">Operational Summary</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Sample data — replaced by GET /api/v1/admin/dashboard (discovery.md §4.3) once the backend exists.
      </p>

      <div className="mt-[var(--space-xl)] grid grid-cols-2 gap-[var(--space-lg)] sm:grid-cols-4">
        {[
          { label: "Pending Applications", value: "0" },
          { label: "Active Artists", value: "0" },
          { label: "Open Licensing Requests", value: "0" },
          { label: "Upcoming Events", value: "0" },
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

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Applicant</th>
              <th className="px-4 py-3 font-semibold">Genre</th>
              <th className="px-4 py-3 font-semibold">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={3}>
                No applications yet.
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div className="mt-[var(--space-lg)] flex gap-2">
        <Badge status="warning">Pending: 0</Badge>
        <Badge status="success">Accepted: 0</Badge>
      </div>
    </div>
  );
}
