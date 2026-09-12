<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

// Ports frontend/src/components/ui/dashboard-shell.tsx — same props shape
// (brand, navGroups, userLabel, logout), same markup structure, styled by
// the .dashboard-shell rules in resources/sass/app.scss instead of Tailwind
// utility classes.
class DashboardShell extends Component
{
    /**
     * @param  array<int, array{heading?: string, items: array<int, array{href: string, label: string}>}>  $navGroups
     */
    public function __construct(
        public string $brand,
        public array $navGroups,
        public string $userLabel,
        public string $logoutRoute,
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.dashboard-shell');
    }
}
