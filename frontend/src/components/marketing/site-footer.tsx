import Link from "next/link";

const columns = [
  {
    heading: "Explore",
    links: [
      { href: "/artists", label: "Artists" },
      { href: "/music", label: "Music" },
      { href: "/events", label: "Events" },
      { href: "/news", label: "News" },
    ],
  },
  {
    heading: "Work with us",
    links: [
      { href: "/artists/submit", label: "Submit your music" },
      { href: "/contact?category=licensing", label: "Licensing" },
      { href: "/contact?category=partnership", label: "Partnerships" },
    ],
  },
  {
    heading: "Company",
    links: [
      { href: "/about", label: "About Tramax" },
      { href: "/contact", label: "Contact" },
    ],
  },
];

export function SiteFooter() {
  return (
    <footer className="mt-auto bg-[var(--color-bg-sunken)] px-6 py-16">
      <div className="mx-auto grid max-w-(--layout-container-max) gap-12 sm:grid-cols-2 md:grid-cols-4">
        <div>
          <p className="font-[var(--font-display)] text-lg font-semibold">Tramax</p>
          <p className="mt-2 max-w-[24ch] text-sm text-[var(--color-text-secondary)]">
            Discover. Develop. Promote.
          </p>
        </div>
        {columns.map((col) => (
          <div key={col.heading}>
            <p className="text-xs font-semibold uppercase tracking-[0.08em] text-[var(--color-text-muted)]">
              {col.heading}
            </p>
            <ul className="mt-3 flex flex-col gap-2">
              {col.links.map((link) => (
                <li key={link.href}>
                  <Link
                    href={link.href}
                    className="text-sm text-[var(--color-text-secondary)] hover:text-[var(--color-text-primary)]"
                  >
                    {link.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        ))}
      </div>
      <p className="mx-auto mt-12 max-w-(--layout-container-max) text-xs text-[var(--color-text-muted)]">
        © {new Date().getFullYear()} Tramax Entertainment Ltd.
      </p>
    </footer>
  );
}
