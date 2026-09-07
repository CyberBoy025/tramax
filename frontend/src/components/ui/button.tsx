import Link from "next/link";
import type { ComponentPropsWithoutRef } from "react";

type Variant = "primary" | "secondary";

const base =
  "inline-flex items-center justify-center gap-2 rounded-[var(--radius-pill)] h-11 px-6 text-sm font-semibold transition-colors duration-[var(--motion-fast)] disabled:opacity-40 disabled:pointer-events-none";

const variants: Record<Variant, string> = {
  primary:
    "bg-[var(--color-accent-primary)] text-[var(--color-accent-on-accent)] hover:brightness-90 active:brightness-80",
  secondary:
    "bg-transparent text-[var(--color-text-primary)] border border-[var(--color-border-strong)] hover:bg-[var(--color-bg-raised)]",
};

type ButtonProps = ComponentPropsWithoutRef<"button"> & {
  variant?: Variant;
};

export function Button({ variant = "primary", className = "", ...props }: ButtonProps) {
  return <button className={`${base} ${variants[variant]} ${className}`} {...props} />;
}

type LinkButtonProps = ComponentPropsWithoutRef<typeof Link> & {
  variant?: Variant;
};

export function LinkButton({ variant = "primary", className = "", ...props }: LinkButtonProps) {
  return <Link className={`${base} ${variants[variant]} ${className}`} {...props} />;
}
