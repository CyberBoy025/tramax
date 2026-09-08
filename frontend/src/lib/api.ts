const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://tramax.local/api/v1";

// Server-side fetch helper (used from Server Components — see discovery.md §4.1
// for the full endpoint list). Returns null on failure so pages can render an
// empty state instead of crashing when the backend isn't reachable.
export async function apiGet<T>(path: string): Promise<T | null> {
  try {
    const res = await fetch(`${API_URL}/${path}`, {
      next: { revalidate: 30 },
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json.data as T;
  } catch {
    return null;
  }
}

// Client-side POST helper for forms (see components/marketing/enquiry-form.tsx).
export async function apiPost<T>(
  path: string,
  body: Record<string, unknown>
): Promise<{ ok: true; data: T } | { ok: false; errors: Record<string, string[]> | null }> {
  try {
    const res = await fetch(`${API_URL}/${path}`, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify(body),
    });
    const json = await res.json().catch(() => null);
    if (!res.ok) {
      return { ok: false, errors: json?.errors ?? null };
    }
    return { ok: true, data: json.data as T };
  } catch {
    return { ok: false, errors: null };
  }
}

export type Artist = {
  id: number;
  artist_name: string;
  slug: string;
  genre: string | null;
  photo_url: string | null;
  status: string;
  biography?: string | null;
  releases?: Release[];
};

export type Release = {
  id: number;
  title: string;
  slug: string;
  type: string;
  cover_art_url: string | null;
  release_date: string | null;
  artist?: { id: number; artist_name: string; slug: string };
  tracks?: { id: number; title: string; track_number: number }[];
  streaming_links?: Record<string, string> | null;
};

export type EventItem = {
  id: number;
  title: string;
  slug: string;
  venue: string | null;
  city: string | null;
  event_date: string | null;
  status: string;
  description?: string | null;
  artists?: { id: number; artist_name: string; slug: string }[];
};

export type NewsPost = {
  id: number;
  title: string;
  slug: string;
  cover_image: string | null;
  published_at: string | null;
  body?: string | null;
};

export type Product = {
  id: number;
  title: string;
  slug: string;
  price: string | null;
  image_url: string | null;
  category: string | null;
  description?: string | null;
};
