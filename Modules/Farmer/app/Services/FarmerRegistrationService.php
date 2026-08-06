<?php

namespace Modules\Farmer\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Repositories\Contracts\FarmerRepositoryInterface;
use Modules\User\Repositories\Contracts\UserRepositoryInterface;

class FarmerRegistrationService
{
    public function __construct(
        private readonly FarmerRepositoryInterface $farmers,
        private readonly UserRepositoryInterface $users,
    ) {}

    public function register(array $data): Farmer
    {
        return DB::transaction(function () use ($data) {
            /** @var User $user */
            $user = $this->users->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => true,
            ]);
            $user->assignRole('farmer');

            $farmer = $this->farmers->create([
                'user_id' => $user->id,
                'region_id' => $data['region_id'],
                'village_id' => $data['village_id'] ?? null,
                'farmer_group_id' => $data['farmer_group_id'] ?? null,
                'name' => $data['name'],
                'phone' => $data['phone'],
                'land_area_ha' => $data['land_area_ha'] ?? null,
                'is_active' => true,
            ]);

            Auth::login($user);

            return $this->farmers->findOrFail($farmer->getKey());
        });
    }
}
