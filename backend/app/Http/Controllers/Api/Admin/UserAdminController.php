<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Super Administrator only, on every action — discovery.md §3's Users &
// Roles row gives every other role "—". This is also, practically, the
// only way a real (non-seeded) staff or artist account gets created —
// there's no public registration flow, by design (§4.2/§4.3 assume
// accounts are provisioned by an admin).
class UserAdminController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()->with('role:id,name')->orderBy('name')->get(['id', 'name', 'email', 'role_id', 'status']);

        return response()->json(['data' => $users]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user->load('role:id,name')]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['nullable', 'string', 'in:Active,Suspended'],
        ]);

        $user = User::create($data);

        return response()->json(['data' => $user->load('role:id,name')], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['sometimes', 'exists:roles,id'],
            'status' => ['nullable', 'string', 'in:Active,Suspended'],
        ]);

        $isSelf = $request->user()->id === $user->id;
        if ($isSelf && array_key_exists('role_id', $data) && $data['role_id'] != $user->role_id) {
            return response()->json(['message' => 'You cannot change your own role.'], 422);
        }
        if ($isSelf && ($data['status'] ?? null) === 'Suspended') {
            return response()->json(['message' => 'You cannot suspend your own account.'], 422);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['data' => $user->fresh()->load('role:id,name')]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }

        $user->delete();

        return response()->json(['data' => ['message' => 'Deleted.']]);
    }
}
