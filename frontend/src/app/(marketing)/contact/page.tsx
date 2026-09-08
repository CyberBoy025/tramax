import { Kicker } from "@/components/ui/kicker";
import { EnquiryForm } from "@/components/marketing/enquiry-form";

export const metadata = { title: "Contact" };

const CATEGORIES = ["General", "Booking", "Media"];

export default async function ContactPage(props: PageProps<"/contact">) {
  const params = await props.searchParams;
  const requested = typeof params.category === "string" ? params.category : "General";
  const category = CATEGORIES.includes(requested) ? requested : "General";

  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Contact</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Get in touch</h1>
      <p className="mt-4 max-w-[56ch] text-[var(--color-text-secondary)]">
        General enquiries, bookings, or media — send us a message and we&apos;ll
        route it to the right team.
      </p>
      <div className="mt-10">
        <EnquiryForm
          endpoint="contact"
          submitLabel="Send Message"
          extra={{ category }}
          fields={[
            { name: "name", label: "Your name" },
            { name: "email", label: "Email", type: "email" },
            { name: "message", label: "Message", type: "textarea" },
          ]}
        />
      </div>
    </section>
  );
}
