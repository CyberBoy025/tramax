import { DashboardShell } from "@/components/ui/dashboard-shell";

// Nav mirrors the Artist Portal sitemap in discovery.md §1.2.
const navGroups = [
  {
    items: [
      { href: "/portal/dashboard", label: "Dashboard" },
      { href: "/portal/profile", label: "My Profile" },
    ],
  },
  {
    heading: "Catalogue",
    items: [
      { href: "/portal/releases", label: "My Releases" },
      { href: "/portal/catalogue", label: "Discography" },
    ],
  },
  {
    heading: "Finance",
    items: [
      { href: "/portal/royalty-statements", label: "Royalty Statements" },
      { href: "/portal/earnings", label: "Earnings Overview" },
    ],
  },
  {
    heading: "More",
    items: [
      { href: "/portal/documents", label: "Documents & Contracts" },
      { href: "/portal/bookings", label: "Bookings" },
      { href: "/portal/notifications", label: "Notifications" },
    ],
  },
];

export default function PortalDashboardLayout({ children }: LayoutProps<"/portal/dashboard">) {
  return (
    <DashboardShell brand="Tramax Portal" navGroups={navGroups} userLabel="Signed in as Artist">
      {children}
    </DashboardShell>
  );
}
