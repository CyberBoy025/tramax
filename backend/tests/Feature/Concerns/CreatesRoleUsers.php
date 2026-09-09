<?php

namespace Tests\Feature\Concerns;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;

// Every RBAC test needs a user per role — this is the one place that
// knows how to build one, so the matrix tests can stay data, not setup
// code. Roles are seeded once per test via RoleSeeder (the same seeder
// that provisions them for real), not hand-rolled here.
trait CreatesRoleUsers
{
    protected function seedRoles(): void
    {
        $this->seed(RoleSeeder::class);
    }

    protected function userWithRole(string $roleName): User
    {
        $role = Role::query()->where('name', $roleName)->firstOrFail();

        return User::factory()->create(['role_id' => $role->id, 'status' => 'Active']);
    }

    protected function superAdmin(): User
    {
        return $this->userWithRole(Role::SUPER_ADMIN);
    }
}
