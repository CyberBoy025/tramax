"use client";

import { useState, type FormEvent } from "react";
import { Button } from "@/components/ui/button";

// Shared login shape for the Artist Portal and Admin Platform (proposal §4.2/§4.3).
export function LoginForm({ endpoint }: { endpoint: string }) {
  const [submitting, setSubmitting] = useState(false);

  function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    // Wired to real session-based auth (proposal §8) once the Phase 4 backend exists.
    console.info(`authenticate → ${endpoint}`, Object.fromEntries(new FormData(event.currentTarget)));
  }

  return (
    <form onSubmit={handleSubmit} className="flex w-full max-w-sm flex-col gap-5">
      <label className="flex flex-col gap-2 text-sm font-medium">
        Email
        <input
          name="email"
          type="email"
          required
          className="h-11 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] px-3 text-sm font-normal outline-none focus:border-[var(--color-accent-primary)]"
        />
      </label>
      <label className="flex flex-col gap-2 text-sm font-medium">
        Password
        <input
          name="password"
          type="password"
          required
          className="h-11 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] px-3 text-sm font-normal outline-none focus:border-[var(--color-accent-primary)]"
        />
      </label>
      <Button type="submit" disabled={submitting}>
        {submitting ? "Signing in…" : "Sign In"}
      </Button>
    </form>
  );
}
