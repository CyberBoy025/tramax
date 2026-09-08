import { redirect } from "next/navigation";

// /portal has no content of its own — send visitors to the dashboard, whose
// client-side auth guard (lib/auth.ts's useAuthGuard) bounces to
// /portal/login if they're not signed in.
export default function PortalIndexPage() {
  redirect("/portal/dashboard");
}
