<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ArtistApplication;
use App\Models\ArtistProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\ApplicationAdminController — same query,
// same validation, same accept->provision-profile business rule, just
// rendering a view and redirecting instead of returning JSON. RBAC is
// enforced by routes/web.php's role: middleware, not here.
class ApplicationController extends Controller
{
    private const STATUSES = ['Submitted', 'Under Review', 'Shortlisted', 'Accepted', 'Rejected'];

    public function index(Request $request): View
    {
        $query = ArtistApplication::query()->orderByDesc('submitted_at');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.applications.index', [
            'applications' => $query->get(),
            'statuses' => self::STATUSES,
            'activeStatus' => $status ?? '',
        ]);
    }

    public function updateStatus(Request $request, ArtistApplication $application): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $application->update($data);

        if ($data['status'] === 'Accepted') {
            ArtistProfile::firstOrCreate(
                ['slug' => Str::slug($application->artist_name)],
                [
                    'artist_name' => $application->artist_name,
                    'genre' => $application->genre,
                    'biography' => $application->biography,
                    'status' => 'Development',
                ]
            );
        }

        return back()->with('success', 'Status updated.');
    }
}
