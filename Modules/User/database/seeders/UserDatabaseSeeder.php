<?php

namespace Modules\User\Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Region\Models\Region;
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
        $this->seedDistrictAdmins();
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

    private function seedDistrictAdmins(): void
    {
        $password = config('digipangan.district_admin.password');

        if (blank($password)) {
            $this->command?->warn('DISTRICT_ADMIN_PASSWORD kosong, akun admin distrik dilewati.');

            return;
        }

        if (app()->isProduction() && in_array(strtolower($password), self::WEAK_PASSWORDS, true)) {
            throw new RuntimeException(
                'DISTRICT_ADMIN_PASSWORD masih memakai nilai default yang lemah. Ganti sebelum seeding di produksi.'
            );
        }

        foreach (['Muting', 'Ulilin', 'Elikobel'] as $districtName) {
            $region = Region::where('name', $districtName)->first();

            if (! $region) {
                continue;
            }

            $email = 'admin.'.$region->slug.'@digipangan.test';
            $districtAdmin = User::withTrashed()->firstOrNew(['email' => $email]);

            if (! $districtAdmin->exists) {
                $districtAdmin->fill([
                    'name' => 'Admin Distrik '.$region->name,
                    'password' => Hash::make($password),
                    'is_active' => true,
                ]);
            }

            $districtAdmin->region_id = $region->id;
            $districtAdmin->save();
            $districtAdmin->syncRoles([UserRole::DistrictAdmin->value]);
        }
    }
}
