// Status badge per ui.md §11 — pairs a semantic color with a text label,
// never color alone, since status meaning must survive for colorblind users.
type Status = "success" | "warning" | "error" | "info";

const styles: Record<Status, string> = {
  success: "bg-[var(--color-state-success)]/16 text-[var(--color-state-success)]",
  warning: "bg-[var(--color-state-warning)]/16 text-[var(--color-state-warning)]",
  error: "bg-[var(--color-state-error)]/16 text-[var(--color-state-error)]",
  info: "bg-[var(--color-state-info)]/16 text-[var(--color-state-info)]",
};

export function Badge({ status, children }: { status: Status; children: React.ReactNode }) {
  return (
    <span
      className={`inline-flex items-center rounded-[var(--radius-pill)] px-2.5 py-1 text-xs font-semibold ${styles[status]}`}
    >
      {children}
    </span>
  );
}
