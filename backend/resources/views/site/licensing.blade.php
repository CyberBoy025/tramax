<x-layouts.public title="Licensing">
    <section class="site-container py-6" style="padding-top: 5rem; padding-bottom: 5rem;">
        <x-kicker>Music Licensing</x-kicker>
        <h1 class="mt-3 fw-semibold display-5">Request a License</h1>
        <p class="mt-3" style="max-width: 56ch; color: var(--color-text-secondary);">
            Filmmakers, advertisers, and media companies can request permission to use Tramax
            music — tell us about your project.
        </p>
        <div class="mt-4">
            @include('site.partials.enquiry-form', [
                'action' => route('site.licensing.store'),
                'submitLabel' => 'Submit Request',
                'fields' => [
                    ['name' => 'company_name', 'label' => 'Company / Organisation'],
                    ['name' => 'contact_person', 'label' => 'Contact person'],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                    ['name' => 'music_required', 'label' => 'Music required', 'required' => false],
                    ['name' => 'project_type', 'label' => 'Project type', 'required' => false],
                    ['name' => 'usage', 'label' => 'Usage', 'required' => false],
                    ['name' => 'duration', 'label' => 'Duration', 'required' => false],
                    ['name' => 'territory', 'label' => 'Territory', 'required' => false],
                    ['name' => 'budget', 'label' => 'Budget', 'required' => false],
                    ['name' => 'message', 'label' => 'Message', 'type' => 'textarea', 'required' => false],
                ],
            ])
        </div>
    </section>
</x-layouts.public>
