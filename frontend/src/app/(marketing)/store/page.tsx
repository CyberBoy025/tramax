import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";

export const metadata = { title: "Store" };

// Catalogue-only for MVP per README.md §06 — checkout is conditional, confirmed at Phase 1.
const products = [
  { slug: "sample-tee", title: "Sample Tramax Tee", meta: "Apparel" },
  { slug: "sample-cap", title: "Sample Tramax Cap", meta: "Apparel" },
];

export default function StorePage() {
  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Store</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Merchandise</h1>
      <p className="mt-4 max-w-[56ch] text-[var(--color-text-secondary)]">
        Catalogue preview — checkout is out of scope for the MVP unless
        confirmed separately (README.md §06).
      </p>
      <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {products.map((product) => (
          <Card key={product.slug} href={`/store/${product.slug}`} title={product.title} meta={product.meta} />
        ))}
      </div>
    </section>
  );
}
