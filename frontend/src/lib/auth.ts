"use client";

import { useEffect, useState, useSyncExternalStore } from "react";
import { useRouter } from "next/navigation";

// Client-side session storage for the Bearer-token auth built in the backend's
// Phase 4 pass (POST /api/v1/auth/login). localStorage, not cookies — this is
// a token-auth API, not session/cookie auth, so there's nothing for the server
// to read; every dashboard route that needs the user is a Client Component.

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://tramax.local/api/v1";
const STORAGE_KEY = "tramax_auth";

export type AuthUser = {
  id: number;
  name: string;
  email: string;
  role: string | null;
};

type StoredAuth = { token: string; user: AuthUser };

export function getStoredAuth(): StoredAuth | null {
  if (typeof window === "undefined") return null;
  try {
    const raw = window.localStorage.getItem(STORAGE_KEY);
    return raw ? (JSON.parse(raw) as StoredAuth) : null;
  } catch {
    return null;
  }
}

function setStoredAuth(auth: StoredAuth) {
  window.localStorage.setItem(STORAGE_KEY, JSON.stringify(auth));
}

export function clearStoredAuth() {
  window.localStorage.removeItem(STORAGE_KEY);
}

export async function login(
  email: string,
  password: string
): Promise<{ ok: true; user: AuthUser } | { ok: false; message: string }> {
  try {
    const res = await fetch(`${API_URL}/auth/login`, {
      method: "POST",
      headers: { "Content-Type": "application/json", Accept: "application/json" },
      body: JSON.stringify({ email, password }),
    });
    const json = await res.json().catch(() => null);

    if (!res.ok) {
      const message =
        json?.errors && Object.values(json.errors).flat()[0]
          ? String(Object.values(json.errors).flat()[0])
          : "Invalid email or password.";
      return { ok: false, message };
    }

    setStoredAuth({ token: json.data.token, user: json.data.user });
    return { ok: true, user: json.data.user as AuthUser };
  } catch {
    return { ok: false, message: "Couldn't reach the server — is the backend running?" };
  }
}

export async function logout(): Promise<void> {
  const auth = getStoredAuth();
  clearStoredAuth();
  if (!auth) return;
  try {
    await fetch(`${API_URL}/auth/logout`, {
      method: "POST",
      headers: { Authorization: `Bearer ${auth.token}`, Accept: "application/json" },
    });
  } catch {
    // Token is already cleared client-side; a failed server-side revoke isn't fatal here.
  }
}

// Unauthenticated GET for Client Components that need public data (e.g. an
// artist picker in an admin form) — deliberately doesn't use lib/api.ts's
// apiGet, which passes Next's `next: { revalidate }` cache option; that's a
// Server Component / RSC-fetch concept, and passing it from a Client
// Component fetch reliably failed to resolve here (confirmed live: the
// exact same request worked fine with the option stripped).
export async function publicGet<T>(path: string): Promise<T | null> {
  try {
    const res = await fetch(`${API_URL}/${path}`, { headers: { Accept: "application/json" } });
    if (!res.ok) return null;
    const json = await res.json();
    return json.data as T;
  } catch {
    return null;
  }
}

// Authenticated fetch helpers for Client Components — attach the stored
// Bearer token automatically. Returns null on any failure (network, 401,
// 403, etc.) so callers can render an empty/denied state rather than crash.
export async function authGet<T>(path: string): Promise<T | null> {
  const auth = getStoredAuth();
  if (!auth) return null;
  try {
    const res = await fetch(`${API_URL}/${path}`, {
      headers: { Authorization: `Bearer ${auth.token}`, Accept: "application/json" },
    });
    if (!res.ok) return null;
    const json = await res.json();
    return json.data as T;
  } catch {
    return null;
  }
}

export type AuthMutateResult<T> =
  | { ok: true; data: T }
  | { ok: false; status: number; message: string };

async function authMutate<T>(
  method: "POST" | "PATCH" | "DELETE",
  path: string,
  body?: Record<string, unknown>
): Promise<AuthMutateResult<T>> {
  const auth = getStoredAuth();
  if (!auth) return { ok: false, status: 401, message: "Not signed in." };
  try {
    const res = await fetch(`${API_URL}/${path}`, {
      method,
      headers: {
        Authorization: `Bearer ${auth.token}`,
        "Content-Type": "application/json",
        Accept: "application/json",
      },
      body: body ? JSON.stringify(body) : undefined,
    });
    const json = await res.json().catch(() => null);
    if (!res.ok) {
      const message =
        json?.errors && Object.values(json.errors).flat()[0]
          ? String(Object.values(json.errors).flat()[0])
          : (json?.message ?? `Request failed (${res.status}).`);
      return { ok: false, status: res.status, message };
    }
    return { ok: true, data: json.data as T };
  } catch {
    return { ok: false, status: 0, message: "Couldn't reach the server." };
  }
}

export const authPost = <T,>(path: string, body: Record<string, unknown>) =>
  authMutate<T>("POST", path, body);
export const authPatch = <T,>(path: string, body: Record<string, unknown>) =>
  authMutate<T>("PATCH", path, body);
export const authDelete = <T,>(path: string) => authMutate<T>("DELETE", path);

// Multipart upload for POST /admin/uploads and /portal/uploads — deliberately
// not routed through authMutate, since a JSON body and a File body need
// different headers (no explicit Content-Type here: the browser sets the
// multipart boundary itself when the body is a FormData).
export async function authUpload<T>(
  path: string,
  file: File,
  context: string
): Promise<AuthMutateResult<T>> {
  const auth = getStoredAuth();
  if (!auth) return { ok: false, status: 401, message: "Not signed in." };
  try {
    const body = new FormData();
    body.append("file", file);
    body.append("context", context);
    const res = await fetch(`${API_URL}/${path}`, {
      method: "POST",
      headers: { Authorization: `Bearer ${auth.token}`, Accept: "application/json" },
      body,
    });
    const json = await res.json().catch(() => null);
    if (!res.ok) {
      const message =
        json?.errors && Object.values(json.errors).flat()[0]
          ? String(Object.values(json.errors).flat()[0])
          : (json?.message ?? `Upload failed (${res.status}).`);
      return { ok: false, status: res.status, message };
    }
    return { ok: true, data: json.data as T };
  } catch {
    return { ok: false, status: 0, message: "Couldn't reach the server." };
  }
}

// Reads the stored session via useSyncExternalStore rather than
// useState+useEffect — this is external mutable state (localStorage), which
// is exactly what that hook is for: it avoids a hydration mismatch (server
// has no localStorage, so it renders the "logged out" snapshot first, then
// syncs to the real value immediately after hydration, before effects run)
// without needing a setState call inside an effect just to copy the value in.
function subscribeToAuthChanges(callback: () => void) {
  window.addEventListener("storage", callback);
  return () => window.removeEventListener("storage", callback);
}

// getSnapshot must return a referentially-stable value when nothing has
// actually changed, or useSyncExternalStore re-renders on every call —
// getStoredAuth() re-parses JSON each time, so cache by the raw string.
let cachedRaw: string | null = null;
let cachedAuth: StoredAuth | null = null;

function getAuthSnapshot(): StoredAuth | null {
  const raw = typeof window === "undefined" ? null : window.localStorage.getItem(STORAGE_KEY);
  if (raw !== cachedRaw) {
    cachedRaw = raw;
    try {
      cachedAuth = raw ? (JSON.parse(raw) as StoredAuth) : null;
    } catch {
      cachedAuth = null;
    }
  }
  return cachedAuth;
}

function getAuthServerSnapshot(): StoredAuth | null {
  return null;
}

/**
 * Guards a dashboard route: redirects to `loginPath` unless a stored session
 * exists whose role is in `allowedRoles`. Returns the authenticated user once
 * resolved, or null while still checking / redirecting.
 *
 * The mount-gate below is deliberate, not a leftover: useSyncExternalStore's
 * hydration commit still fires its own effects using the server snapshot
 * (null) before the store-correction re-render's effects run, so a redirect
 * check without this gate fires once, wrongly, on every hard navigation —
 * confirmed live (login succeeds, /auth/me works, but reloading the
 * dashboard bounced straight back to /login even with a valid stored
 * token). Waiting one extra render past mount avoids acting on that
 * transient null.
 */
export function useAuthGuard(allowedRoles: string[], loginPath: string): AuthUser | null {
  const router = useRouter();
  const auth = useSyncExternalStore(subscribeToAuthChanges, getAuthSnapshot, getAuthServerSnapshot);
  const isValid = !!auth && !!auth.user.role && allowedRoles.includes(auth.user.role);

  const [mounted, setMounted] = useState(false);
  // eslint-disable-next-line react-hooks/set-state-in-effect -- intentional mount-gate, see doc comment above
  useEffect(() => setMounted(true), []);

  useEffect(() => {
    if (mounted && !isValid) router.replace(loginPath);
  }, [mounted, isValid, router, loginPath]);

  return mounted && isValid ? (auth as StoredAuth).user : null;
}
