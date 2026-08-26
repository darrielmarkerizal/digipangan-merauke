<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $adminRole = Role::query()->where('name', 'admin')->where('guard_name', 'web')->first();

        if ($adminRole === null) {
            return;
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        User::withTrashed()->role('admin')->get()->each(function (User $user) use ($superAdmin): void {
            $user->assignRole($superAdmin);
            $user->removeRole('admin');
        });

        $adminRole->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void {}
};
