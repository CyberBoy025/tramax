"use client";

import { useState } from "react";
import { authUpload } from "@/lib/auth";

type UploadResult = { url: string; path: string };

// A file input that uploads immediately on selection and writes the
// resulting URL into a hidden input — so every existing form's
// Object.fromEntries(new FormData(form)) submit logic keeps working
// unchanged; this just replaces "paste a URL" with "pick a file".
export function ImageUploadField({
  name,
  label,
  defaultValue,
  uploadPath,
  context,
}: {
  name: string;
  label: string;
  defaultValue?: string | null;
  uploadPath: "admin/uploads" | "portal/uploads";
  context: "artists" | "releases" | "products" | "news";
}) {
  const [url, setUrl] = useState(defaultValue ?? "");
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleFileChange(event: React.ChangeEvent<HTMLInputElement>) {
    const file = event.target.files?.[0];
    if (!file) return;

    setUploading(true);
    setError(null);
    const result = await authUpload<UploadResult>(uploadPath, file, context);
    setUploading(false);

    if (!result.ok) {
      setError(result.message);
      return;
    }
    setUrl(result.data.url);
  }

  return (
    <div className="flex flex-col gap-1 text-xs font-medium">
      {label}
      <input type="hidden" name={name} value={url} />
      <div className="flex items-center gap-3">
        {url && (
          // eslint-disable-next-line @next/next/no-img-element -- arbitrary uploaded-file URLs, not a static/known asset next/image can optimize
          <img
            src={url}
            alt=""
            className="h-12 w-12 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] object-cover"
          />
        )}
        <input
          type="file"
          accept="image/*"
          onChange={handleFileChange}
          disabled={uploading}
          className="text-xs text-[var(--color-text-secondary)] file:mr-3 file:rounded-[var(--radius-sm)] file:border-0 file:bg-[var(--color-bg-raised)] file:px-3 file:py-2 file:text-xs file:font-medium"
        />
        {uploading && <span className="text-[var(--color-text-muted)]">Uploading…</span>}
        {url && !uploading && (
          <button
            type="button"
            onClick={() => setUrl("")}
            className="text-[var(--color-state-error)] hover:underline"
          >
            Remove
          </button>
        )}
      </div>
      {error && <p className="text-[var(--color-state-error)]">{error}</p>}
    </div>
  );
}
