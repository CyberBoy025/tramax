import { Kicker } from "@/components/ui/kicker";

export const metadata = { title: "About" };

export default function AboutPage() {
  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>About Tramax</Kicker>
      <h1 className="mt-3 max-w-[20ch] text-4xl font-semibold sm:text-5xl">
        Our story, mission, and what we do
      </h1>
      <p className="mt-6 max-w-[64ch] text-[var(--color-text-secondary)]">
        Placeholder copy — final content (company story, mission, vision, and
        capabilities) is supplied by Tramax per the proposal&apos;s content
        checklist (§14) and dropped in here during Phase 3.
      </p>
    </section>
  );
}
