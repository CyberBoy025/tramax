<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

// Session-based ("web" guard) login for the Blade admin dashboard and
// artist portal — NOT a replacement for Sanctum, which stays exactly as it
// is for /api/* (external/future API consumers per Conversion-README.md
// §17/§18). Blade pages are rendered per-request with no client-side token
// storage, so they need Laravel's ordinary cookie/session auth instead;
// this is additive, not a rewrite of the existing auth architecture.
class AuthController extends Controller
{
    private const ADMIN_ROLES = [
        Role::SUPER_ADMIN,
        Role::MANAGEMENT,
        Role::AR_MANAGER,
        Role::FINANCE,
        Role::CONTENT_MANAGER,
    ];

    public function showAdminLogin(): View
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        return $this->attempt($request, self::ADMIN_ROLES, 'admin.login', 'admin.dashboard',
            'This account isn\'t an admin account — use the artist login instead.');
    }

    public function showPortalLogin(): View
    {
        return view('auth.portal-login');
    }

    public function portalLogin(Request $request): RedirectResponse
    {
        return $this->attempt($request, [Role::ARTIST], 'portal.login', 'portal.dashboard',
            'This account isn\'t an artist account — use the admin login instead.');
    }

    private function attempt(
        Request $request,
        array $allowedRoles,
        string $loginRoute,
        string $redirectRoute,
        string $wrongAreaMessage
    ): RedirectResponse {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->with('role')->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        if ($user->status !== 'Active') {
            return back()->withErrors(['email' => 'This account is suspended.'])->onlyInput('email');
        }

        if (! $user->hasRole(...$allowedRoles)) {
            return back()->withErrors(['email' => $wrongAreaMessage])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->route($redirectRoute);
    }

    public function logout(Request $request): RedirectResponse
    {
        $loginRoute = Auth::user()?->hasRole(Role::ARTIST) ? 'portal.login' : 'admin.login';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route($loginRoute);
    }
}
