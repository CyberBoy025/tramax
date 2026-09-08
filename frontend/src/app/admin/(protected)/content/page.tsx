"use client";

import { useEffect, useState, type FormEvent } from "react";
import { authGet, authPost, authPatch, authDelete, getStoredAuth } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

type NewsPost = {
  id: number;
  title: string;
  body: string | null;
  cover_image: string | null;
  status: string;
  published_at: string | null;
};

const STATUSES = ["Draft", "Published"];
const STATUS_BADGE: Record<string, "success" | "info"> = { Published: "success", Draft: "info" };

export default function AdminContentPage() {
  const [posts, setPosts] = useState<NewsPost[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [editing, setEditing] = useState<NewsPost | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const role = getStoredAuth()?.user.role ?? null;
  const canWrite = role === "Super Administrator" || role === "Content Manager";
  const canDelete = role === "Super Administrator";

  function load() {
    document.title = "Content · Tramax Admin";
    authGet<NewsPost[]>("admin/news").then((data) => {
      setPosts(data);
      setLoaded(true);
    });
  }

  useEffect(load, []);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);

    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form));
    const body = Object.fromEntries(Object.entries(data).filter(([, v]) => v !== ""));

    const result = editing
      ? await authPatch(`admin/news/${editing.id}`, body)
      : await authPost("admin/news", body);

    setSubmitting(false);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    form.reset();
    setEditing(null);
    load();
  }

  async function handleDelete(id: number) {
    const result = await authDelete(`admin/news/${id}`);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    if (editing?.id === id) setEditing(null);
    load();
  }

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (posts === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Content</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Content Management (discovery.md §3: Super
          Administrator, Management, or Content Manager only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Content</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        News posts — scoped to what discovery.md §2 models; generic Pages/Media management
        (proposal §4.3) isn&apos;t built. Publishing sets the live date automatically.
      </p>

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Title</th>
              <th className="px-4 py-3 font-semibold">Published</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              {canWrite && <th className="px-4 py-3 font-semibold"></th>}
            </tr>
          </thead>
          <tbody>
            {posts.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={canWrite ? 4 : 3}>
                  No posts yet.
                </td>
              </tr>
            )}
            {posts.map((p) => (
              <tr key={p.id} className="border-b border-[var(--color-border-default)] last:border-0">
                <td className="px-4 py-3">{p.title}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">
                  {p.published_at ? new Date(p.published_at).toLocaleDateString() : "—"}
                </td>
                <td className="px-4 py-3">
                  <Badge status={STATUS_BADGE[p.status] ?? "info"}>{p.status}</Badge>
                </td>
                {canWrite && (
                  <td className="px-4 py-3 flex gap-3">
                    <button
                      type="button"
                      onClick={() => setEditing(p)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      Edit
                    </button>
                    {canDelete && (
                      <button
                        type="button"
                        onClick={() => handleDelete(p.id)}
                        className="text-xs text-[var(--color-state-error)] hover:underline"
                      >
                        Delete
                      </button>
                    )}
                  </td>
                )}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {canWrite && (
        <div className="mt-[var(--space-xl)] rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
          <div className="flex items-center justify-between">
            <p className="font-semibold">{editing ? `Edit "${editing.title}"` : "New Post"}</p>
            {editing && (
              <button
                type="button"
                onClick={() => setEditing(null)}
                className="text-xs text-[var(--color-text-secondary)] hover:underline"
              >
                Cancel
              </button>
            )}
          </div>
          <form key={editing?.id ?? "new"} onSubmit={handleSubmit} className="mt-[var(--space-md)] grid gap-[var(--space-md)]">
            <label className="flex flex-col gap-1 text-xs font-medium">
              Title
              <input
                name="title"
                required
                defaultValue={editing?.title}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Cover Image URL
              <input
                name="cover_image"
                defaultValue={editing?.cover_image ?? ""}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Body
              <textarea
                name="body"
                rows={6}
                defaultValue={editing?.body ?? ""}
                className="rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 py-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Status
              <select
                name="status"
                defaultValue={editing?.status ?? "Draft"}
                className="h-10 w-48 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              >
                {STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
              </select>
            </label>
            <div>
              <Button type="submit" disabled={submitting}>
                {submitting ? "Saving…" : editing ? "Save Changes" : "Add Post"}
              </Button>
            </div>
          </form>
          {error && <p className="mt-3 text-sm text-[var(--color-state-error)]">{error}</p>}
        </div>
      )}
    </div>
  );
}
