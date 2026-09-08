"use client";

import { useEffect, useState } from "react";
import { Badge } from "@/components/ui/badge";
import { authGet } from "@/lib/auth";

type Me = {
  id: number;
  name: string;
  email: string;
  role: string;
  artist_profile: { id: number; artist_name: string; slug: string } | null;
};

export default function PortalDashboardPage() {
  const [me, setMe] = useState<Me | null>(null);

  useEffect(() => {
    document.title = "Artist Dashboard · Tramax Entertainment";
    authGet<Me>("auth/me").then(setMe);
  }, []);

  return (
    <div>
      <h1 className="text-2xl font-semibold">Welcome back{me ? `, ${me.name}` : ""}</h1>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        Signed in via GET /api/v1/auth/me. Releases, royalty, and earnings endpoints
        aren&apos;t built yet (§4.2) — those stay placeholders until that pass.
      </p>

      <div className="mt-[var(--space-xl)] grid grid-cols-2 gap-[var(--space-lg)] sm:grid-cols-4">
        {[
          { label: "Releases", value: "—" },
          { label: "Tracks", value: "—" },
          { label: "Streams", value: "—" },
          { label: "Earnings", value: "—" },
        ].map((stat) => (
          <div
            key={stat.label}
            className="rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]"
          >
            <p className="font-data text-2xl font-semibold">{stat.value}</p>
            <p className="mt-1 text-xs text-[var(--color-text-secondary)]">{stat.label}</p>
          </div>
        ))}
      </div>

      <div className="mt-[var(--space-xl)] rounded-[var(--radius-md)] border border-[var(--color-border-default)] bg-[var(--color-bg-raised)] p-[var(--space-lg)]">
        <div className="flex items-center justify-between">
          <p className="font-semibold">Linked Artist Profile</p>
          {me?.artist_profile ? (
            <Badge status="success">{me.artist_profile.artist_name}</Badge>
          ) : (
            <Badge status="info">Not linked</Badge>
          )}
        </div>
        <p className="mt-2 text-sm text-[var(--color-text-secondary)]">
          {me?.artist_profile
            ? "Releases submitted here will appear on the public catalogue once published."
            : "This account isn't linked to a public ArtistProfile yet — an admin links one during application review."}
        </p>
      </div>
    </div>
  );
}
