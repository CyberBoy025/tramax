import { redirect } from "next/navigation";

// /admin has no content of its own — send visitors to the dashboard, whose
// client-side auth guard (lib/auth.ts's useAuthGuard) bounces to /admin/login
// if they're not signed in.
export default function AdminIndexPage() {
  redirect("/admin/dashboard");
}
