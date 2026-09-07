import { DashboardShell } from "@/components/ui/dashboard-shell";

// Nav mirrors the Admin Platform sitemap in discovery.md §1.3 and the
// module list in discovery.md §2. Which items render for the signed-in
// user is governed by the RBAC matrix in discovery.md §3 — not encoded
// here yet, since there's no auth/session backend to check against.
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
  return (
    <DashboardShell brand="Tramax Admin" navGroups={navGroups} userLabel="Signed in as Super Administrator">
      {children}
    </DashboardShell>
  );
}
