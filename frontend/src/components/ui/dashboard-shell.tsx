import Link from "next/link";

// Shared operational-surface shell for the Artist Portal and Admin Platform.
// Sets data-surface="operational" so the density override in globals.css
// (tighter spacing, per ui.md §5) applies to every dashboard screen.
export function DashboardShell({
  brand,
  navGroups,
  userLabel,
  onLogout,
  children,
}: {
  brand: string;
  navGroups: { heading?: string; items: { href: string; label: string }[] }[];
  userLabel: string;
  onLogout?: () => void;
  children: React.ReactNode;
}) {
  return (
    <div data-surface="operational" className="flex min-h-screen">
      <aside className="flex w-64 shrink-0 flex-col border-r border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
        <p className="font-[var(--font-display)] text-base font-semibold">{brand}</p>
        <nav className="mt-[var(--space-xl)] flex flex-1 flex-col gap-[var(--space-lg)]">
          {navGroups.map((group, i) => (
            <div key={group.heading ?? i} className="flex flex-col gap-1">
              {group.heading && (
                <p className="mb-1 text-[11px] font-semibold uppercase tracking-[0.08em] text-[var(--color-text-muted)]">
                  {group.heading}
                </p>
              )}
              {group.items.map((item) => (
                <Link
                  key={item.href}
                  href={item.href}
                  className="rounded-[var(--radius-sm)] px-3 py-2 text-sm text-[var(--color-text-secondary)] transition-colors duration-[var(--motion-fast)] hover:bg-[var(--color-bg-base)] hover:text-[var(--color-text-primary)]"
                >
                  {item.label}
                </Link>
              ))}
            </div>
          ))}
        </nav>
        <div className="flex flex-col gap-2 border-t border-[var(--color-border-default)] pt-[var(--space-md)]">
          <p className="text-xs text-[var(--color-text-muted)]">{userLabel}</p>
          {onLogout && (
            <button
              type="button"
              onClick={onLogout}
              className="self-start text-xs font-medium text-[var(--color-accent-primary)] hover:underline"
            >
              Log out
            </button>
          )}
        </div>
      </aside>
      <main className="flex-1 p-[var(--space-2xl)]">{children}</main>
    </div>
  );
}
