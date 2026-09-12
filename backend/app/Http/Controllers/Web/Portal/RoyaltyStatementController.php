<?php

namespace App\Http\Controllers\Web\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Blade equivalent of Api\Portal\PortalRoyaltyStatementController —
// read-only, own statements only.
class RoyaltyStatementController extends Controller
{
    public function index(Request $request): View
    {
        $profile = $request->user()->artistProfile;

        $statements = $profile
            ? $profile->royaltyStatements()->with('lineItems')->orderByDesc('period_end')->get()
            : null;

        return view('portal.royalty-statements.index', ['statements' => $statements]);
    }
}
