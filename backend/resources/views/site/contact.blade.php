<x-layouts.public title="Contact">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Contact</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">Get in touch</h1>
        <p class="mt-3" style="max-width: 56ch; color: var(--color-text-secondary);">
            General enquiries, bookings, or media — send us a message and we'll route it to the
            right team.
        </p>
        <div class="mt-4">
            @include('site.partials.enquiry-form', [
                'action' => route('site.contact.store'),
                'submitLabel' => 'Send Message',
                'extra' => ['category' => $category],
                'fields' => [
                    ['name' => 'name', 'label' => 'Your name'],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                    ['name' => 'message', 'label' => 'Message', 'type' => 'textarea'],
                ],
            ])
        </div>
    </section>
</x-layouts.public>
