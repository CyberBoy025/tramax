// Eyebrow/kicker label, per ui.md §11 — precedes every section heading sitewide.
export function Kicker({ children }: { children: React.ReactNode }) {
  return (
    <span className="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.08em] text-[var(--color-text-secondary)]">
      <span aria-hidden className="h-1.5 w-1.5 bg-[var(--color-accent-primary)]" />
      {children}
    </span>
  );
}
