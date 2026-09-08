import { Kicker } from "@/components/ui/kicker";
import { EnquiryForm } from "@/components/marketing/enquiry-form";

export const metadata = { title: "Partnerships" };

export default function PartnershipsPage() {
  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Partner With Tramax</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Partnership Enquiry</h1>
      <p className="mt-4 max-w-[56ch] text-[var(--color-text-secondary)]">
        Distributors, publishers, brands, and media companies — tell us about
        a potential partnership.
      </p>
      <div className="mt-10">
        <EnquiryForm
          endpoint="partners"
          submitLabel="Send Enquiry"
          fields={[
            { name: "organization_name", label: "Organisation" },
            { name: "contact_person", label: "Contact person" },
            { name: "email", label: "Email", type: "email" },
            { name: "type", label: "Organisation type", required: false },
            { name: "message", label: "Message", type: "textarea", required: false },
          ]}
        />
      </div>
    </section>
  );
}
