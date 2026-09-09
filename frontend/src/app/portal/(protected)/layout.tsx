"use client";

import { useRouter } from "next/navigation";
import { DashboardShell } from "@/components/ui/dashboard-shell";
import { useAuthGuard, logout } from "@/lib/auth";

// Nav mirrors the Artist Portal sitemap in discovery.md §1.2, collapsed to
// what actually has a backing endpoint: "Discography" and "Earnings
// Overview" were the same data as Releases/Royalty Statements under a
// different label, so they're merged in rather than duplicated. "Documents
// & Contracts" is dropped — there's no Document entity in discovery.md §2's
// data model (unlike every other screen here), so it'd be a nav link with
// nothing real behind it; same MVP-depth judgment call as Distribution
// being status-only elsewhere in this build.
const navGroups = [
  {
    items: [
      { href: "/portal/dashboard", label: "Dashboard" },
      { href: "/portal/profile", label: "My Profile" },
    ],
  },
  {
    heading: "Catalogue & Finance",
    items: [
      { href: "/portal/releases", label: "My Releases" },
      { href: "/portal/royalty-statements", label: "Royalty & Earnings" },
    ],
  },
  {
    heading: "More",
    items: [
      { href: "/portal/bookings", label: "Bookings" },
      { href: "/portal/notifications", label: "Notifications" },
    ],
  },
];

export default function PortalProtectedLayout({ children }: { children: React.ReactNode }) {
  const router = useRouter();
  const user = useAuthGuard(["Artist"], "/portal/login");

  async function handleLogout() {
    await logout();
    router.replace("/portal/login");
  }

  if (!user) {
    return (
      <div className="flex min-h-screen items-center justify-center text-sm text-[var(--color-text-secondary)]">
        Checking session…
      </div>
    );
  }

  return (
    <DashboardShell
      brand="Tramax Portal"
      navGroups={navGroups}
      userLabel={`Signed in as ${user.name}`}
      onLogout={handleLogout}
    >
      {children}
    </DashboardShell>
  );
}
