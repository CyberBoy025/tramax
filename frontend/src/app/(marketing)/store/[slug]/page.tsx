import { notFound } from "next/navigation";
import { Kicker } from "@/components/ui/kicker";
import { apiGet, type Product } from "@/lib/api";

export default async function ProductPage(props: PageProps<"/store/[slug]">) {
  const { slug } = await props.params;
  const product = await apiGet<Product>(`products/${slug}`);

  if (!product) notFound();

  return (
    <article className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Store</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">{product.title}</h1>
      <p className="mt-2 text-sm text-[var(--color-text-secondary)]">{product.category ?? "Merchandise"}</p>
      {product.price && (
        <p className="mt-4 font-data text-2xl font-semibold">₦{Number(product.price).toLocaleString()}</p>
      )}
      <p className="mt-6 max-w-[56ch] text-[var(--color-text-secondary)]">
        {product.description || "Catalogue preview — checkout is out of scope for the MVP (README.md §06)."}
      </p>
    </article>
  );
}
