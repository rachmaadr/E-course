<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $ownerRole = Role::create([
            'name' => 'owner'
        ]);

        $studentRole = Role::create([
            'name' => 'student'
        ]);

        $teacherRole = Role::create([
            'name' => 'teacher'
        ]);

        //Akun Super Admin untuk mengelola data awal
        $userOwner = User::create([
            'name' => 'Raihandika',
            'occupation' => 'Educator',
            'avatar' => 'images/default-avatar.png',
            'email' => 'pisang@diginey.com',
            'password' => bcrypt('admin123'),
        ]);

        $userOwner->assignRole($ownerRole);
    }
}
