import { Kicker } from "@/components/ui/kicker";
import { Card } from "@/components/ui/card";
import { apiGet, type Product } from "@/lib/api";

export const metadata = { title: "Store" };

export default async function StorePage() {
  const products = (await apiGet<Product[]>("products")) ?? [];

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Store</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Merchandise</h1>
      <p className="mt-4 max-w-[56ch] text-[var(--color-text-secondary)]">
        Catalogue preview — checkout is out of scope for the MVP unless
        confirmed separately (README.md §06).
      </p>
      <div className="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        {products.length === 0 && (
          <p className="text-sm text-[var(--color-text-muted)]">No products published yet.</p>
        )}
        {products.map((product) => (
          <Card
            key={product.slug}
            href={`/store/${product.slug}`}
            title={product.title}
            meta={product.category ?? undefined}
          >
            {product.price && (
              <p className="mt-2 font-data text-sm text-[var(--color-text-secondary)]">
                ₦{Number(product.price).toLocaleString()}
              </p>
            )}
          </Card>
        ))}
      </div>
    </section>
  );
}
