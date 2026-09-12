<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\PartnerAdminController.
class PartnerController extends Controller
{
    private const STATUSES = ['New', 'In Discussion', 'Active'];

    public function index(Request $request): View
    {
        $query = Partner::query()->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.partners.index', [
            'partners' => $query->get(),
            'statuses' => self::STATUSES,
            'activeStatus' => $status ?? '',
        ]);
    }

    public function updateStatus(Request $request, Partner $partner): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $partner->update($data);

        return back()->with('success', 'Status updated.');
    }

    public function destroy(Partner $partner): RedirectResponse
    {
        $partner->delete();

        return redirect()->route('admin.partners.index')->with('success', 'Partner deleted.');
    }
}
