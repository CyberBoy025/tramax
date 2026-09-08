"use client";

import { useState, type FormEvent } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { login, clearStoredAuth } from "@/lib/auth";

// Shared login shape for the Artist Portal and Admin Platform (proposal §4.2/§4.3).
// Both hit the same backend endpoint (auth/login is shared across all seven
// roles, per discovery.md §3) — `allowedRoles` and `redirectTo` are what make
// this component behave differently on /portal/login vs /admin/login.
export function LoginForm({
  allowedRoles,
  redirectTo,
  wrongAreaMessage,
}: {
  allowedRoles: string[];
  redirectTo: string;
  wrongAreaMessage: string;
}) {
  const router = useRouter();
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);

    const form = new FormData(event.currentTarget);
    const result = await login(String(form.get("email")), String(form.get("password")));

    if (!result.ok) {
      setError(result.message);
      setSubmitting(false);
      return;
    }

    if (!result.user.role || !allowedRoles.includes(result.user.role)) {
      clearStoredAuth();
      setError(wrongAreaMessage);
      setSubmitting(false);
      return;
    }

    router.push(redirectTo);
  }

  return (
    <form onSubmit={handleSubmit} className="flex w-full max-w-sm flex-col gap-5">
      <label className="flex flex-col gap-2 text-sm font-medium">
        Email
        <input
          name="email"
          type="email"
          required
          autoComplete="email"
          className="h-11 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] px-3 text-sm font-normal outline-none focus:border-[var(--color-accent-primary)]"
        />
      </label>
      <label className="flex flex-col gap-2 text-sm font-medium">
        Password
        <input
          name="password"
          type="password"
          required
          autoComplete="current-password"
          className="h-11 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] px-3 text-sm font-normal outline-none focus:border-[var(--color-accent-primary)]"
        />
      </label>
      {error && <p className="text-sm text-[var(--color-state-error)]">{error}</p>}
      <Button type="submit" disabled={submitting}>
        {submitting ? "Signing in…" : "Sign In"}
      </Button>
    </form>
  );
}
