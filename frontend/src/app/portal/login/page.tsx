import { LoginForm } from "@/components/ui/login-form";

export const metadata = { title: "Artist Login" };

export default function PortalLoginPage() {
  return (
    <div className="flex min-h-screen flex-col items-center justify-center px-6">
      <p className="font-[var(--font-display)] text-lg font-semibold">Tramax Portal</p>
      <h1 className="mt-2 text-2xl font-semibold">Artist Sign In</h1>
      <div className="mt-8">
        <LoginForm
          allowedRoles={["Artist"]}
          redirectTo="/portal/dashboard"
          wrongAreaMessage="This account isn't an artist account — use the admin login instead."
        />
      </div>
    </div>
  );
}
