<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade port of frontend/src/app/(marketing)/partnerships/page.tsx — same
// validation as Api\PartnerController::store.
class PartnerController extends Controller
{
    public function show(): View
    {
        return view('site.partnerships');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'organization_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
        ]);

        Partner::create($data);

        return back()->with('submitted', true);
    }
}
