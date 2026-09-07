"use client";

import { useState, type FormEvent } from "react";
import { Button } from "@/components/ui/button";

type Field = { name: string; label: string; type?: "text" | "email" | "textarea" };

// Shared shape for the four enquiry-routing forms named in the proposal §4.1:
// general/booking/media contact, artist submission, licensing, partnership.
export function EnquiryForm({
  fields,
  submitLabel,
  endpoint,
}: {
  fields: Field[];
  submitLabel: string;
  endpoint: string;
}) {
  const [status, setStatus] = useState<"idle" | "submitted">("idle");

  function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    // Wired to a real endpoint once the Phase 4 API (see discovery.md §4.1) exists.
    console.info(`submit → ${endpoint}`, Object.fromEntries(new FormData(event.currentTarget)));
    setStatus("submitted");
  }

  if (status === "submitted") {
    return (
      <p className="rounded-[var(--radius-md)] border border-[var(--color-state-success)]/40 bg-[var(--color-state-success)]/10 p-4 text-sm text-[var(--color-state-success)]">
        Thanks — we&apos;ve received your submission and will be in touch.
      </p>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="flex max-w-lg flex-col gap-5">
      {fields.map((field) => (
        <label key={field.name} className="flex flex-col gap-2 text-sm font-medium">
          {field.label}
          {field.type === "textarea" ? (
            <textarea
              name={field.name}
              required
              rows={5}
              className="rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] p-3 text-sm font-normal outline-none focus:border-[var(--color-accent-primary)]"
            />
          ) : (
            <input
              name={field.name}
              type={field.type ?? "text"}
              required
              className="h-11 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-3 text-sm font-normal outline-none focus:border-[var(--color-accent-primary)]"
            />
          )}
        </label>
      ))}
      <Button type="submit" className="self-start">
        {submitLabel}
      </Button>
    </form>
  );
}
