import Link from "next/link";
import { LinkButton } from "@/components/ui/button";

const links = [
  { href: "/artists", label: "Artists" },
  { href: "/music", label: "Music" },
  { href: "/videos", label: "Videos" },
  { href: "/events", label: "Events" },
  { href: "/news", label: "News" },
  { href: "/store", label: "Store" },
  { href: "/about", label: "About" },
];

// Pill navbar per ui.md §11 — inset on the homepage hero, standard full-width
// bar on inner pages (both share the same token set).
export function SiteHeader() {
  return (
    <header className="sticky top-0 z-40 mx-auto mt-4 flex w-[calc(100%-2rem)] max-w-(--layout-container-max) items-center justify-between rounded-[var(--radius-pill)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)]/85 px-6 py-3 backdrop-blur">
      <Link href="/" className="font-[var(--font-display)] text-lg font-semibold">
        Tramax
      </Link>
      <nav className="hidden items-center gap-6 md:flex">
        {links.map((link) => (
          <Link
            key={link.href}
            href={link.href}
            className="text-sm font-medium text-[var(--color-text-secondary)] transition-colors duration-[var(--motion-fast)] hover:text-[var(--color-text-primary)]"
          >
            {link.label}
          </Link>
        ))}
      </nav>
      <LinkButton href="/contact" className="hidden sm:inline-flex">
        Contact
      </LinkButton>
    </header>
  );
}
