<x-layouts.public title="Partnerships">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Partner With Tramax</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">Partnership Enquiry</h1>
        <p class="mt-3" style="max-width: 56ch; color: var(--color-text-secondary);">
            Distributors, publishers, brands, and media companies — tell us about a potential
            partnership.
        </p>
        <div class="mt-4">
            @include('site.partials.enquiry-form', [
                'action' => route('site.partnerships.store'),
                'submitLabel' => 'Send Enquiry',
                'fields' => [
                    ['name' => 'organization_name', 'label' => 'Organisation'],
                    ['name' => 'contact_person', 'label' => 'Contact person'],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                    ['name' => 'type', 'label' => 'Organisation type', 'required' => false],
                    ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => false],
                ],
            ])
        </div>
    </section>
</x-layouts.public>
