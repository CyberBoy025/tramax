"use client";

import { useRouter } from "next/navigation";
import { DashboardShell } from "@/components/ui/dashboard-shell";
import { useAuthGuard, logout } from "@/lib/auth";

const ADMIN_ROLES = [
  "Super Administrator",
  "Management",
  "A&R / Artist Manager",
  "Finance",
  "Content Manager",
];

// Nav mirrors the Admin Platform sitemap in discovery.md §1.3 and the
// module list in discovery.md §2. Which items render for the signed-in
// user is governed by the RBAC matrix in discovery.md §3 — not encoded
// here yet, since only Applications/Artists/Releases have admin endpoints
// built (see backend routes/api.php).
const navGroups = [
  { items: [{ href: "/admin/dashboard", label: "Dashboard" }] },
  {
    heading: "Artists",
    items: [
      { href: "/admin/applications", label: "Applications" },
      { href: "/admin/artists", label: "Artists" },
    ],
  },
  {
    heading: "Catalogue",
    items: [
      { href: "/admin/releases", label: "Music / Releases" },
      { href: "/admin/rights", label: "Rights & Catalogue" },
    ],
  },
  {
    heading: "Business",
    items: [
      { href: "/admin/royalty", label: "Royalty & Revenue" },
      { href: "/admin/licensing", label: "Licensing Requests" },
      { href: "/admin/partners", label: "Partners" },
    ],
  },
  {
    heading: "Public site",
    items: [
      { href: "/admin/events", label: "Events" },
      { href: "/admin/store", label: "Merchandise" },
      { href: "/admin/content", label: "News / Pages / Media" },
    ],
  },
  {
    heading: "System",
    items: [
      { href: "/admin/users", label: "Users & Roles" },
      { href: "/admin/audit-log", label: "Audit Log" },
      { href: "/admin/reports", label: "Reports" },
    ],
  },
];

export default function AdminDashboardLayout({ children }: LayoutProps<"/admin/dashboard">) {
  const router = useRouter();
  const user = useAuthGuard(ADMIN_ROLES, "/admin/login");

  async function handleLogout() {
    await logout();
    router.replace("/admin/login");
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
      brand="Tramax Admin"
      navGroups={navGroups}
      userLabel={`Signed in as ${user.name} · ${user.role}`}
      onLogout={handleLogout}
    >
      {children}
    </DashboardShell>
  );
}
