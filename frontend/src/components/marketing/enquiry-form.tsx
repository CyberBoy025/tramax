"use client";

import { useState, type FormEvent } from "react";
import { Button } from "@/components/ui/button";
import { apiPost } from "@/lib/api";

type Field = {
  name: string;
  label: string;
  type?: "text" | "email" | "textarea";
  required?: boolean;
};

// Shared shape for the four enquiry-routing forms named in the proposal §4.1:
// general/booking/media contact, artist submission, licensing, partnership.
export function EnquiryForm({
  fields,
  submitLabel,
  endpoint,
  extra,
}: {
  fields: Field[];
  submitLabel: string;
  endpoint: string;
  /** Fixed fields sent alongside the form's own fields (e.g. category). */
  extra?: Record<string, string>;
}) {
  const [status, setStatus] = useState<"idle" | "submitting" | "submitted" | "error">("idle");
  const [error, setError] = useState<string | null>(null);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setStatus("submitting");
    setError(null);

    const body = { ...extra, ...Object.fromEntries(new FormData(event.currentTarget)) };
    const result = await apiPost(endpoint, body);

    if (result.ok) {
      setStatus("submitted");
    } else {
      setStatus("error");
      setError(
        result.errors
          ? Object.values(result.errors).flat().join(" ")
          : "Something went wrong sending this — please try again."
      );
    }
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
              required={field.required ?? true}
              rows={5}
              className="rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] p-3 text-sm font-normal outline-none focus:border-[var(--color-accent-primary)]"
            />
          ) : (
            <input
              name={field.name}
              type={field.type ?? "text"}
              required={field.required ?? true}
              className="h-11 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-3 text-sm font-normal outline-none focus:border-[var(--color-accent-primary)]"
            />
          )}
        </label>
      ))}
      {error && <p className="text-sm text-[var(--color-state-error)]">{error}</p>}
      <Button type="submit" disabled={status === "submitting"} className="self-start">
        {status === "submitting" ? "Sending…" : submitLabel}
      </Button>
    </form>
  );
}
