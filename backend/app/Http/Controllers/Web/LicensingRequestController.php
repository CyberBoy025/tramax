<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\LicensingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Admin\LicensingRequestAdminController.
class LicensingRequestController extends Controller
{
    private const STATUSES = ['New', 'In Review', 'Approved', 'Declined'];

    public function index(Request $request): View
    {
        $query = LicensingRequest::query()->with('release:id,title')->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.licensing.index', [
            'requests' => $query->get(),
            'statuses' => self::STATUSES,
            'activeStatus' => $status ?? '',
        ]);
    }

    public function updateStatus(Request $request, LicensingRequest $licensingRequest): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)],
        ]);

        $licensingRequest->update($data);

        return back()->with('success', 'Status updated.');
    }

    public function destroy(LicensingRequest $licensingRequest): RedirectResponse
    {
        $licensingRequest->delete();

        return redirect()->route('admin.licensing.index')->with('success', 'Licensing request deleted.');
    }
}
