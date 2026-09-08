"use client";

import { useEffect, useState, type FormEvent } from "react";
import { authGet, authPost, authPatch, authDelete, getStoredAuth } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";

type Role = { id: number; name: string };

type AdminUser = {
  id: number;
  name: string;
  email: string;
  role_id: number;
  status: string;
  role: Role | null;
};

const STATUSES = ["Active", "Suspended"];
const STATUS_BADGE: Record<string, "success" | "warning"> = { Active: "success", Suspended: "warning" };

export default function AdminUsersPage() {
  const [users, setUsers] = useState<AdminUser[] | null>(null);
  const [roles, setRoles] = useState<Role[]>([]);
  const [loaded, setLoaded] = useState(false);
  const [editing, setEditing] = useState<AdminUser | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const currentUser = getStoredAuth()?.user ?? null;
  const role = currentUser?.role ?? null;
  const canWrite = role === "Super Administrator";
  const isEditingSelf = !!editing && !!currentUser && editing.id === currentUser.id;

  function load() {
    document.title = "Users & Roles · Tramax Admin";
    Promise.all([authGet<AdminUser[]>("admin/users"), authGet<Role[]>("admin/roles")]).then(
      ([usersData, rolesData]) => {
        setUsers(usersData);
        setRoles(rolesData ?? []);
        setLoaded(true);
      }
    );
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
      ? await authPatch(`admin/users/${editing.id}`, body)
      : await authPost("admin/users", body);

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
    const result = await authDelete(`admin/users/${id}`);
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

  if (users === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Users &amp; Roles</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your role doesn&apos;t have access to Users &amp; Roles (discovery.md §3: Super
          Administrator only).
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Users &amp; Roles</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Staff and artist accounts are provisioned here — there&apos;s no public registration
        flow (discovery.md §4.2/§4.3). You can&apos;t change your own role, suspend yourself, or
        delete your own account.
      </p>

      <div className="mt-[var(--space-xl)] overflow-x-auto rounded-[var(--radius-md)] border border-[var(--color-border-default)]">
        <table className="w-full text-sm">
          <thead>
            <tr className="border-b border-[var(--color-border-default)] text-left text-xs uppercase tracking-[0.06em] text-[var(--color-text-secondary)]">
              <th className="px-4 py-3 font-semibold">Name</th>
              <th className="px-4 py-3 font-semibold">Email</th>
              <th className="px-4 py-3 font-semibold">Role</th>
              <th className="px-4 py-3 font-semibold">Status</th>
              {canWrite && <th className="px-4 py-3 font-semibold"></th>}
            </tr>
          </thead>
          <tbody>
            {users.length === 0 && (
              <tr>
                <td className="px-4 py-6 text-[var(--color-text-muted)]" colSpan={canWrite ? 5 : 4}>
                  No users yet.
                </td>
              </tr>
            )}
            {users.map((u) => (
              <tr key={u.id} className="border-b border-[var(--color-border-default)] last:border-0">
                <td className="px-4 py-3">
                  {u.name}
                  {currentUser?.id === u.id && (
                    <span className="ml-2 text-xs text-[var(--color-text-muted)]">(you)</span>
                  )}
                </td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{u.email}</td>
                <td className="px-4 py-3 text-[var(--color-text-secondary)]">{u.role?.name ?? "—"}</td>
                <td className="px-4 py-3">
                  <Badge status={STATUS_BADGE[u.status] ?? "info"}>{u.status}</Badge>
                </td>
                {canWrite && (
                  <td className="px-4 py-3 flex gap-3">
                    <button
                      type="button"
                      onClick={() => setEditing(u)}
                      className="text-xs text-[var(--color-accent-primary)] hover:underline"
                    >
                      Edit
                    </button>
                    {currentUser?.id !== u.id && (
                      <button
                        type="button"
                        onClick={() => handleDelete(u.id)}
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
            <p className="font-semibold">{editing ? `Edit "${editing.name}"` : "New User"}</p>
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
          <form key={editing?.id ?? "new"} onSubmit={handleSubmit} className="mt-[var(--space-md)] grid gap-[var(--space-md)] sm:grid-cols-2">
            <label className="flex flex-col gap-1 text-xs font-medium">
              Name
              <input
                name="name"
                required
                defaultValue={editing?.name}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Email
              <input
                type="email"
                name="email"
                required
                defaultValue={editing?.email}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Password {editing && <span className="text-[var(--color-text-muted)]">(leave blank to keep current)</span>}
              <input
                type="password"
                name="password"
                required={!editing}
                minLength={8}
                placeholder={editing ? "••••••••" : undefined}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
              />
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Role
              {isEditingSelf && <span className="text-[var(--color-text-muted)]">(you can&apos;t change your own role)</span>}
              <select
                name="role_id"
                required
                disabled={isEditingSelf}
                defaultValue={editing?.role_id ?? ""}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm disabled:opacity-60"
              >
                {!editing && <option value="" disabled>Select a role…</option>}
                {roles.map((r) => (
                  <option key={r.id} value={r.id}>{r.name}</option>
                ))}
              </select>
            </label>
            <label className="flex flex-col gap-1 text-xs font-medium">
              Status
              {isEditingSelf && <span className="text-[var(--color-text-muted)]">(you can&apos;t suspend yourself)</span>}
              <select
                name="status"
                disabled={isEditingSelf}
                defaultValue={editing?.status ?? "Active"}
                className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm disabled:opacity-60"
              >
                {STATUSES.map((s) => <option key={s} value={s}>{s}</option>)}
              </select>
            </label>
            <div className="flex items-end sm:col-span-2">
              <Button type="submit" disabled={submitting}>
                {submitting ? "Saving…" : editing ? "Save Changes" : "Add User"}
              </Button>
            </div>
          </form>
          {error && <p className="mt-3 text-sm text-[var(--color-state-error)]">{error}</p>}
        </div>
      )}
    </div>
  );
}
