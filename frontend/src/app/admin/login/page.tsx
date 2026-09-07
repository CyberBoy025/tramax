import { LoginForm } from "@/components/ui/login-form";

export const metadata = { title: "Admin Login" };

export default function AdminLoginPage() {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center px-6">
      <p className="font-[var(--font-display)] text-lg font-semibold">Tramax Admin</p>
      <h1 className="mt-2 text-2xl font-semibold">Sign In</h1>
      <div className="mt-8">
        <LoginForm endpoint="/api/v1/admin/auth/login" />
      </div>
    </div>
  );
}
