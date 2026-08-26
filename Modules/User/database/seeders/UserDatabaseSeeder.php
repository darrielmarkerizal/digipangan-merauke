<?php

namespace Modules\User\Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\UserRole;
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

        $superAdmin = Role::firstOrCreate(['name' => UserRole::SuperAdmin->value, 'guard_name' => 'web']);
        $districtAdmin = Role::firstOrCreate(['name' => UserRole::DistrictAdmin->value, 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => UserRole::Farmer->value, 'guard_name' => 'web']);

        $superAdmin->syncPermissions(PermissionEnum::values());
        $districtAdmin->syncPermissions(PermissionEnum::forDistrictAdmin());

        $this->seedInitialAdmin();
    }

    private function seedInitialAdmin(): void
    {
        $email = config('digipangan.admin.email');
        $password = config('digipangan.admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL atau ADMIN_PASSWORD kosong, akun pengelola dilewati.');

            return;
        }

        if (app()->isProduction() && in_array(strtolower($password), self::WEAK_PASSWORDS, true)) {
            throw new RuntimeException(
                'ADMIN_PASSWORD masih memakai nilai default yang lemah. Ganti sebelum seeding di produksi.'
            );
        }

        $initialUser = User::withTrashed()->firstOrNew(['email' => $email]);

        if ($initialUser->exists) {
            $this->command?->info("Akun pengelola {$email} sudah ada, tidak diubah.");
        } else {
            $initialUser->fill([
                'name' => config('digipangan.admin.name'),
                'password' => Hash::make($password),
                'is_active' => true,
            ])->save();
        }

        $initialUser->syncRoles([UserRole::SuperAdmin->value]);
    }
}
