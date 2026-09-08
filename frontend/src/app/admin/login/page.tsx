import { LoginForm } from "@/components/ui/login-form";

export const metadata = { title: "Admin Login" };

// Every staff role except Artist and Partner/External User, per discovery.md §3 —
// those two don't have an admin-platform login (Artist uses /portal, and no
// Partner-facing surface has been built yet).
const ADMIN_ROLES = [
  "Super Administrator",
  "Management",
  "A&R / Artist Manager",
  "Finance",
  "Content Manager",
];

export default function AdminLoginPage() {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center px-6">
      <p className="font-[var(--font-display)] text-lg font-semibold">Tramax Admin</p>
      <h1 className="mt-2 text-2xl font-semibold">Sign In</h1>
      <div className="mt-8">
        <LoginForm
          allowedRoles={ADMIN_ROLES}
          redirectTo="/admin/dashboard"
          wrongAreaMessage="This account doesn't have admin access."
        />
      </div>
    </div>
  );
}
