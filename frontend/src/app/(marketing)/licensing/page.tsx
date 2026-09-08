import { Kicker } from "@/components/ui/kicker";
import { EnquiryForm } from "@/components/marketing/enquiry-form";

export const metadata = { title: "Licensing" };

export default function LicensingPage() {
  return (
    <section className="mx-auto max-w-(--layout-container-max) px-6 py-20">
      <Kicker>Music Licensing</Kicker>
      <h1 className="mt-3 text-4xl font-semibold sm:text-5xl">Request a License</h1>
      <p className="mt-4 max-w-[56ch] text-[var(--color-text-secondary)]">
        Filmmakers, advertisers, and media companies can request permission to
        use Tramax music — tell us about your project.
      </p>
      <div className="mt-10">
        <EnquiryForm
          endpoint="licensing-requests"
          submitLabel="Submit Request"
          fields={[
            { name: "company_name", label: "Company / Organisation" },
            { name: "contact_person", label: "Contact person" },
            { name: "email", label: "Email", type: "email" },
            { name: "music_required", label: "Music required", required: false },
            { name: "project_type", label: "Project type", required: false },
            { name: "usage", label: "Usage", required: false },
            { name: "duration", label: "Duration", required: false },
            { name: "territory", label: "Territory", required: false },
            { name: "budget", label: "Budget", required: false },
            { name: "message", label: "Message", type: "textarea", required: false },
          ]}
        />
      </div>
    </section>
  );
}
