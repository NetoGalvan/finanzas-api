<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Crear roles
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);

        // Crear permisos (esto es solo un ejemplo)
        $adminPermission    = Permission::create(['name' => 'all']);
        $permissionUpload = Permission::create(['name' => 'upload-docs']);
        $permissionDownload = Permission::create(['name' => 'download-docs']);

        // Asignar permisos a roles
        $admin->givePermissionTo($adminPermission);
        $admin->givePermissionTo($permissionUpload);
        $admin->givePermissionTo($permissionDownload);
        $user->givePermissionTo($permissionUpload);
        $user->givePermissionTo($permissionDownload);
    }
}
