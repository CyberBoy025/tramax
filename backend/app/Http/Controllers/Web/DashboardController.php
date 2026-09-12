<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use App\Models\Product;
use App\Models\Role;
use App\Services\ReportsSummaryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade port of frontend/src/app/admin/(protected)/dashboard/page.tsx —
// same stat-building and role-scoped fallback logic, now built
// server-side from ReportsSummaryService instead of a client fetch.
class DashboardController extends Controller
{
    private const SCOPE_NOTE = [
        'full' => 'Live operational summary across every module.',
        'artist_management' => 'Scoped to your domain — Artist Management, Catalogue, Events.',
        'finance' => 'Scoped to your domain — Royalty, Licensing.',
    ];

    public function __construct(private ReportsSummaryService $reports) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        // Content Manager has no Reports access (discovery.md §3) — fall
        // back to their own two modules directly instead of showing nothing.
        if ($user->hasRole(Role::CONTENT_MANAGER)) {
            return view('admin.dashboard', [
                'note' => 'Scoped to your domain — Store, Content.',
                'stats' => [
                    ['label' => 'Published Products', 'value' => Product::where('status', 'Published')->count()],
                    ['label' => 'Draft Products', 'value' => Product::where('status', 'Draft')->count()],
                    ['label' => 'Published News', 'value' => NewsPost::where('status', 'Published')->count()],
                    ['label' => 'Draft News', 'value' => NewsPost::where('status', 'Draft')->count()],
                ],
            ]);
        }

        $summary = $this->reports->buildFor($user);

        return view('admin.dashboard', [
            'note' => self::SCOPE_NOTE[$summary['scope']] ?? '',
            'stats' => $this->buildStats($summary),
        ]);
    }

    private function buildStats(array $summary): array
    {
        $stats = [];

        if (isset($summary['applications'])) {
            $byStatus = $summary['applications']['by_status'];
            $pending = ($byStatus['Submitted'] ?? 0) + ($byStatus['Under Review'] ?? 0);
            $stats[] = ['label' => 'Pending Applications', 'value' => $pending];
        }
        if (isset($summary['artists'])) {
            $stats[] = ['label' => 'Artists', 'value' => $summary['artists']['total']];
        }
        if (isset($summary['releases'])) {
            $stats[] = ['label' => 'Releases', 'value' => $summary['releases']['total']];
        }
        if (isset($summary['events'])) {
            $stats[] = ['label' => 'Upcoming Events', 'value' => $summary['events']['by_status']['Upcoming'] ?? 0];
        }
        if (isset($summary['licensing_requests'])) {
            $open = $summary['licensing_requests']['total'] - ($summary['licensing_requests']['by_status']['Declined'] ?? 0);
            $stats[] = ['label' => 'Open Licensing Requests', 'value' => $open];
        }
        if (isset($summary['royalty'])) {
            $stats[] = ['label' => 'Total Royalty Revenue', 'value' => '₦'.number_format($summary['royalty']['total_revenue'])];
        }
        if (isset($summary['users'])) {
            $stats[] = ['label' => 'Users', 'value' => $summary['users']['total']];
        }

        return array_slice($stats, 0, 4);
    }
}
