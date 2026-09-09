"use client";

import { useEffect, useState } from "react";
import { authGet, authPatch } from "@/lib/auth";

type PortalNotification = {
  id: number;
  title: string;
  body: string | null;
  read_at: string | null;
  created_at: string;
};

export default function PortalNotificationsPage() {
  const [notifications, setNotifications] = useState<PortalNotification[] | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [markingId, setMarkingId] = useState<number | null>(null);

  function load() {
    document.title = "Notifications · Tramax Portal";
    authGet<PortalNotification[]>("portal/notifications").then((data) => {
      setNotifications(data);
      setLoaded(true);
    });
  }

  useEffect(load, []);

  async function markRead(id: number) {
    setMarkingId(id);
    await authPatch(`portal/notifications/${id}/read`, {});
    setMarkingId(null);
    load();
  }

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (notifications === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">Notifications</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your account isn&apos;t linked to a public artist profile yet.
        </p>
      </div>
    );
  }

  return (
    <div>
      <h1 className="text-2xl font-semibold">Notifications</h1>

      <div className="mt-[var(--space-lg)] flex flex-col gap-3">
        {notifications.length === 0 && (
          <p className="text-sm text-[var(--color-text-muted)]">No notifications yet.</p>
        )}
        {notifications.map((n) => (
          <div
            key={n.id}
            className={`rounded-[var(--radius-md)] border p-[var(--space-lg)] ${
              n.read_at
                ? "border-[var(--color-border-default)] bg-[var(--color-bg-raised)]"
                : "border-[var(--color-accent-primary)]/40 bg-[var(--color-accent-primary)]/5"
            }`}
          >
            <div className="flex items-start justify-between gap-4">
              <div>
                <p className="font-semibold">{n.title}</p>
                {n.body && <p className="mt-1 text-sm text-[var(--color-text-secondary)]">{n.body}</p>}
                <p className="mt-2 text-xs text-[var(--color-text-muted)]">
                  {new Date(n.created_at).toLocaleString()}
                </p>
              </div>
              {!n.read_at && (
                <button
                  type="button"
                  onClick={() => markRead(n.id)}
                  disabled={markingId === n.id}
                  className="shrink-0 text-xs text-[var(--color-accent-primary)] hover:underline"
                >
                  {markingId === n.id ? "Marking…" : "Mark as read"}
                </button>
              )}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
