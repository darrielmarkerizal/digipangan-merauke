<?php

namespace Modules\User\Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class UserDatabaseSeeder extends Seeder
{
    private const WEAK_PASSWORDS = ['password', 'secret', '12345678', 'admin'];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionEnum::values() as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $districtAdmin = Role::firstOrCreate(['name' => 'admin_distrik', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'farmer', 'guard_name' => 'web']);

        $superAdmin->syncPermissions(PermissionEnum::values());
        $admin->syncPermissions(PermissionEnum::forAdmin());
        $districtAdmin->syncPermissions(PermissionEnum::forDistrictAdmin());

        $this->seedInitialAdmin();
    }

    private function seedInitialAdmin(): void
    {
        $email = config('digipangan.admin.email');
        $password = config('digipangan.admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL atau ADMIN_PASSWORD kosong, akun admin dilewati.');

            return;
        }

        if (app()->isProduction() && in_array(strtolower($password), self::WEAK_PASSWORDS, true)) {
            throw new RuntimeException(
                'ADMIN_PASSWORD masih memakai nilai default yang lemah. Ganti sebelum seeding di produksi.'
            );
        }

        $admin = User::withTrashed()->firstOrNew(['email' => $email]);

        if ($admin->exists) {
            $this->command?->info("Akun admin {$email} sudah ada, tidak diubah.");
        } else {
            $admin->fill([
                'name' => config('digipangan.admin.name'),
                'password' => Hash::make($password),
                'is_active' => true,
            ])->save();
        }

        $admin->syncRoles(['super_admin']);
    }
}
