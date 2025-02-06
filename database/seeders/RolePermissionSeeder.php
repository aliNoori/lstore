<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    protected array $rolesAndPermissions = [
        'admin' => ['edit', 'read', 'delete', 'create'],
        'citizen' => ['read'],
        'editor' => ['edit', 'read', 'create']
    ];

    public function run(): void
    {
        foreach ($this->rolesAndPermissions as $roleName => $permissions) {
            // ایجاد یا پیدا کردن نقش
            $role = Role::firstOrCreate(['name' => $roleName]);

            foreach ($permissions as $permissionName) {
                // ایجاد یا پیدا کردن مجوز
                $permission = Permission::firstOrCreate(['name' => $permissionName]);

                // تخصیص مجوز به نقش در صورت نیاز
                if (!$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }
        }
    }
}
