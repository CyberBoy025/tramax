"use client";

import { useEffect, useState, type FormEvent } from "react";
import { authGet, authPatch } from "@/lib/auth";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { ImageUploadField } from "@/components/ui/image-upload-field";

type Profile = {
  id: number;
  artist_name: string;
  slug: string;
  genre: string | null;
  biography: string | null;
  photo_url: string | null;
  status: string;
};

export default function PortalProfilePage() {
  const [profile, setProfile] = useState<Profile | null>(null);
  const [loaded, setLoaded] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [saved, setSaved] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  function load() {
    document.title = "My Profile · Tramax Portal";
    authGet<Profile>("portal/profile").then((data) => {
      setProfile(data);
      setLoaded(true);
    });
  }

  useEffect(load, []);

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setSubmitting(true);
    setError(null);
    setSaved(false);

    const form = event.currentTarget;
    const data = Object.fromEntries(new FormData(form));

    const result = await authPatch("portal/profile", data);
    setSubmitting(false);
    if (!result.ok) {
      setError(result.message);
      return;
    }
    setSaved(true);
    load();
  }

  if (!loaded) {
    return <p className="text-sm text-[var(--color-text-secondary)]">Loading…</p>;
  }

  if (profile === null) {
    return (
      <div>
        <h1 className="text-2xl font-semibold">My Profile</h1>
        <p className="mt-4 rounded-[var(--radius-md)] border border-[var(--color-state-error)]/40 bg-[var(--color-state-error)]/10 p-4 text-sm text-[var(--color-state-error)]">
          Your account isn&apos;t linked to a public artist profile yet — an admin links one when
          your application is accepted (discovery.md §3: Artist Management &amp; Applications).
        </p>
      </div>
    );
  }

  return (
    <div>
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-semibold">My Profile</h1>
        <Badge status={profile.status === "Active" ? "success" : "info"}>{profile.status}</Badge>
      </div>
      <p className="mt-1 text-sm text-[var(--color-text-secondary)]">
        {profile.artist_name} · tramax.com/artists/{profile.slug}. Name, URL, and status are set
        by an admin — everything else here is yours to edit.
      </p>

      <form onSubmit={handleSubmit} className="mt-[var(--space-xl)] grid max-w-2xl gap-[var(--space-md)]">
        <label className="flex flex-col gap-1 text-xs font-medium">
          Genre
          <input
            name="genre"
            defaultValue={profile.genre ?? ""}
            className="h-10 rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 text-sm"
          />
        </label>
        <ImageUploadField
          name="photo_url"
          label="Photo"
          defaultValue={profile.photo_url}
          uploadPath="portal/uploads"
          context="artists"
        />
        <label className="flex flex-col gap-1 text-xs font-medium">
          Biography
          <textarea
            name="biography"
            rows={5}
            defaultValue={profile.biography ?? ""}
            className="rounded-[var(--radius-sm)] border border-[var(--color-border-default)] bg-[var(--color-bg-base)] px-2 py-2 text-sm"
          />
        </label>
        <div className="flex items-center gap-3">
          <Button type="submit" disabled={submitting}>
            {submitting ? "Saving…" : "Save Changes"}
          </Button>
          {saved && <span className="text-xs text-[var(--color-state-success)]">Saved.</span>}
        </div>
        {error && <p className="text-sm text-[var(--color-state-error)]">{error}</p>}
      </form>
    </div>
  );
}
