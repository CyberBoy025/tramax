"use client";

import { useEffect, useState } from "react";
import { Badge } from "@/components/ui/badge";
import { authGet } from "@/lib/auth";

type Profile = { id: number; artist_name: string; slug: string; status: string } | null;
type Release = { id: number; title: string; status: string };
type RoyaltyStatement = { id: number; artist_share: string | number };
type PortalEvent = { id: number; title: string; status: string };
type PortalNotification = { id: number; read_at: string | null };

export default function PortalDashboardPage() {
  const [profile, setProfile] = useState<Profile>(null);
  const [releases, setReleases] = useState<Release[] | null>(null);
  const [royalty, setRoyalty] = useState<RoyaltyStatement[] | null>(null);
  const [events, setEvents] = useState<PortalEvent[] | null>(null);
  const [notifications, setNotifications] = useState<PortalNotification[] | null>(null);

  useEffect(() => {
    document.title = "Artist Dashboard · Tramax Entertainment";
    Promise.all([
      authGet<Profile>("portal/profile"),
      authGet<Release[]>("portal/releases"),
      authGet<RoyaltyStatement[]>("portal/royalty-statements"),
      authGet<PortalEvent[]>("portal/events"),
      authGet<PortalNotification[]>("portal/notifications"),
    ]).then(([p, r, rs, e, n]) => {
      setProfile(p);
      setReleases(r);
      setRoyalty(rs);
      setEvents(e);
      setNotifications(n);
    });
  }, []);

  const totalEarnings = royalty?.reduce((sum, s) => sum + Number(s.artist_share), 0) ?? null;
  const upcomingBookings = events?.filter((e) => e.status === "Upcoming").length ?? null;
  const unreadNotifications = notifications?.filter((n) => n.read_at === null).length ?? null;

  const stats = [
    { label: "Releases", value: releases?.length },
    { label: "Upcoming Bookings", value: upcomingBookings },
    { label: "Total Earnings", value: totalEarnings != null ? `₦${totalEarnings.toLocaleString()}` : undefined },
    { label: "Unread Notifications", value: unreadNotifications },
  ];

  return (
    <div>
      <h1 className="text-2xl font-semibold">Welcome back{profile ? `, ${profile.artist_name}` : ""}</h1>

      <div className="mt-[var(--space-xl)] grid grid-cols-2 gap-[var(--space-lg)] sm:grid-cols-4">
        {stats.map((stat) => (
          <div
            key={stat.label}
            className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]"
          >
            <p className="font-data text-2xl font-semibold">{stat.value ?? "…"}</p>
            <p className="mt-1 text-xs text-[var(--color-text-secondary)]">{stat.label}</p>
          </div>
        ))}
      </div>

      <div className="mt-[var(--space-xl)] rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
        <div className="flex items-center justify-between">
          <p className="font-semibold">Linked Artist Profile</p>
          {profile ? (
            <Badge status="success">{profile.artist_name}</Badge>
          ) : (
            <Badge status="info">Not linked</Badge>
          )}
        </div>
        <p className="mt-2 text-sm text-[var(--color-text-secondary)]">
          {profile
            ? "Releases submitted from My Releases appear here as Draft until an admin publishes them."
            : "This account isn't linked to a public ArtistProfile yet — an admin links one when your application is accepted."}
        </p>
      </div>
    </div>
  );
}
