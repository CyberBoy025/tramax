<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

// Read-only. Roles are the seven fixed, code-defined roles from discovery.md
// §3 (Role::ALL) — the permission each one carries is wired into
// routes/api.php, not stored data, so there's no create/edit/delete here.
// This endpoint exists only to populate the role picker on the Users form.
class RoleAdminController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Role::query()->orderBy('name')->get(['id', 'name'])]);
    }
}
