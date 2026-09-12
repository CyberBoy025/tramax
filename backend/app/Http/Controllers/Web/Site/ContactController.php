<?php

namespace App\Http\Controllers\Web\Site;

use App\Http\Controllers\Controller;
use App\Models\ContactEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade port of frontend/src/app/(marketing)/contact/page.tsx — same
// ?category= query-string default as the React page, and the same
// validation as Api\ContactController::store.
class ContactController extends Controller
{
    private const CATEGORIES = ['General', 'Booking', 'Media'];

    public function show(Request $request): View
    {
        $requested = $request->query('category');
        $category = in_array($requested, self::CATEGORIES, true) ? $requested : 'General';

        return view('site.contact', ['category' => $category]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'category' => ['nullable', 'string', 'in:'.implode(',', self::CATEGORIES)],
            'message' => ['required', 'string'],
        ]);

        ContactEnquiry::create($data + ['category' => $data['category'] ?? 'General']);

        return back()->with('submitted', true);
    }
}
