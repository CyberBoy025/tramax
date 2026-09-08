<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

// Local-dev-only demo accounts, one per role, so login/RBAC can actually be
// tested end to end. Password is intentionally simple and identical for all
// of them — never seed anything like this in a real environment.
class UserSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Role::ALL as $roleName) {
            $role = Role::where('name', $roleName)->first();
            $email = strtolower(str_replace([' ', '&', '/'], ['-', 'and', ''], $roleName)).'@tramax.test';
            $email = preg_replace('/-+/', '-', $email);

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "Demo {$roleName}",
                    'password' => 'password',
                    'role_id' => $role->id,
                    'status' => 'Active',
                ]
            );
        }
    }
}
