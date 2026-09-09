<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArtistApplication;
use App\Models\ArtistProfile;
use App\Models\Event;
use App\Models\LicensingRequest;
use App\Models\NewsPost;
use App\Models\Partner;
use App\Models\Product;
use App\Models\Release;
use App\Models\RightsRecord;
use App\Models\Role;
use App\Models\RoyaltyStatement;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Read-only aggregate summaries (README.md §4 "Analytics — release/artist/
// revenue/activity summaries for admin dashboard") — counts and sums over
// the existing module tables, not a new entity. discovery.md §3 gives A&R
// and Finance "Read (own scope)": since neither role is tied to a subset of
// records the way an Artist is to their own artist_profile, "own scope"
// here means their own functional domain — the modules they already have
// Manage/Full on elsewhere in the matrix. Super Admin and Management get
// everything.
class ReportsAdminController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $role = $request->user()->role->name;

        $sections = match ($role) {
            Role::AR_MANAGER => [
                'scope' => 'artist_management',
                'artists' => $this->artistsSummary(),
                'applications' => $this->applicationsSummary(),
                'releases' => $this->releasesSummary(),
                'rights_records' => $this->rightsSummary(),
                'events' => $this->eventsSummary(),
            ],
            Role::FINANCE => [
                'scope' => 'finance',
                'royalty' => $this->royaltySummary(),
                'licensing_requests' => $this->licensingSummary(),
            ],
            default => [
                'scope' => 'full',
                'artists' => $this->artistsSummary(),
                'applications' => $this->applicationsSummary(),
                'releases' => $this->releasesSummary(),
                'rights_records' => $this->rightsSummary(),
                'events' => $this->eventsSummary(),
                'royalty' => $this->royaltySummary(),
                'licensing_requests' => $this->licensingSummary(),
                'partners' => $this->partnersSummary(),
                'products' => $this->productsSummary(),
                'news' => $this->newsSummary(),
                'users' => $this->usersSummary(),
            ],
        };

        return response()->json(['data' => $sections + ['generated_at' => now()->toIso8601String()]]);
    }

    private function countsByStatus(string $model, string $column = 'status'): array
    {
        return $model::query()->select($column, DB::raw('count(*) as count'))
            ->groupBy($column)
            ->pluck('count', $column)
            ->all();
    }

    private function artistsSummary(): array
    {
        return [
            'total' => ArtistProfile::count(),
            'by_status' => $this->countsByStatus(ArtistProfile::class),
        ];
    }

    private function applicationsSummary(): array
    {
        return [
            'total' => ArtistApplication::count(),
            'by_status' => $this->countsByStatus(ArtistApplication::class),
        ];
    }

    private function releasesSummary(): array
    {
        return [
            'total' => Release::count(),
            'by_status' => $this->countsByStatus(Release::class),
            'by_type' => $this->countsByStatus(Release::class, 'type'),
        ];
    }

    private function rightsSummary(): array
    {
        return [
            'total' => RightsRecord::count(),
            'by_copyright_status' => $this->countsByStatus(RightsRecord::class, 'copyright_status'),
            'by_licensing_status' => $this->countsByStatus(RightsRecord::class, 'licensing_status'),
        ];
    }

    private function eventsSummary(): array
    {
        return [
            'total' => Event::count(),
            'by_status' => $this->countsByStatus(Event::class),
        ];
    }

    private function royaltySummary(): array
    {
        $totals = RoyaltyStatement::query()
            ->selectRaw('count(*) as statements, coalesce(sum(total_revenue),0) as total_revenue, coalesce(sum(company_share),0) as company_share, coalesce(sum(artist_share),0) as artist_share')
            ->first();

        return [
            'total' => (int) $totals->statements,
            'total_revenue' => (float) $totals->total_revenue,
            'company_share' => (float) $totals->company_share,
            'artist_share' => (float) $totals->artist_share,
            'by_status' => $this->countsByStatus(RoyaltyStatement::class),
        ];
    }

    private function licensingSummary(): array
    {
        return [
            'total' => LicensingRequest::count(),
            'by_status' => $this->countsByStatus(LicensingRequest::class),
        ];
    }

    private function partnersSummary(): array
    {
        return [
            'total' => Partner::count(),
            'by_status' => $this->countsByStatus(Partner::class),
        ];
    }

    private function productsSummary(): array
    {
        return [
            'total' => Product::count(),
            'by_status' => $this->countsByStatus(Product::class),
        ];
    }

    private function newsSummary(): array
    {
        return [
            'total' => NewsPost::count(),
            'by_status' => $this->countsByStatus(NewsPost::class),
        ];
    }

    private function usersSummary(): array
    {
        return [
            'total' => User::count(),
            'by_role' => User::query()
                ->join('roles', 'roles.id', '=', 'users.role_id')
                ->select('roles.name', DB::raw('count(*) as count'))
                ->groupBy('roles.name')
                ->pluck('count', 'roles.name')
                ->all(),
        ];
    }
}
