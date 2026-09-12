<div class="dashboard-shell" data-surface="operational">
    <aside class="dashboard-shell__sidebar">
        <p class="dashboard-shell__brand mb-0">{{ $brand }}</p>
        <nav class="dashboard-shell__nav">
            @foreach ($navGroups as $group)
                <div class="d-flex flex-column gap-1">
                    @isset($group['heading'])
                        <p class="dashboard-shell__nav-heading mb-1">{{ $group['heading'] }}</p>
                    @endisset
                    @foreach ($group['items'] as $item)
                        <a
                            href="{{ $item['href'] }}"
                            class="dashboard-shell__nav-link {{ request()->is(ltrim($item['href'], '/').'*') ? 'is-active' : '' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </div>
            @endforeach
        </nav>
        <div class="dashboard-shell__footer">
            <p class="dashboard-shell__user-label mb-0">{{ $userLabel }}</p>
            <form method="POST" action="{{ $logoutRoute }}">
                @csrf
                <button type="submit" class="dashboard-shell__logout">Log out</button>
            </form>
        </div>
    </aside>
    <main class="dashboard-shell__main">
        {{ $slot }}
    </main>
</div>
