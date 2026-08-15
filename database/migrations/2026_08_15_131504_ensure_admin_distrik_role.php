<?php

use App\Enums\Permission as PermissionEnum;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionEnum::values() as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $districtAdmin = Role::firstOrCreate(['name' => 'admin_distrik', 'guard_name' => 'web']);
        $districtAdmin->syncPermissions(PermissionEnum::forDistrictAdmin());
    }

    public function down(): void
    {
        // No destructive rollback needed
    }
};
