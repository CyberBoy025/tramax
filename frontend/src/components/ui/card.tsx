import Link from "next/link";

// Generic content card (artist / release / event / news), per ui.md §11.
export function Card({
  href,
  title,
  meta,
  children,
}: {
  href: string;
  title: string;
  meta?: string;
  children?: React.ReactNode;
}) {
  return (
    <Link
      href={href}
      className="block rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-6 transition-colors duration-[var(--motion-fast)] hover:border-[var(--color-border-strong)]"
    >
      <h3 className="text-lg font-semibold">{title}</h3>
      {meta && (
        <p className="mt-1 text-xs text-[var(--color-text-secondary)]">{meta}</p>
      )}
      {children}
    </Link>
  );
}
