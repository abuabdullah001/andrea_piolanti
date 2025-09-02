<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'owner']);

        //User Assign admin role
        $admin_user = User::where('email', 'admin@admin.com')->first();
        if ($admin_user) {
            $admin_user->assignRole('admin');
        }
        $admin_user = User::where('email', 'user@user.com')->first();
        if ($admin_user) {
            $admin_user->assignRole('user');
        }
        $admin_user = User::where('email', 'owner@owner.com')->first();
        if ($admin_user) {
            $admin_user->assignRole('owner');
        }
    }
}
