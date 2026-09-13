<?php

namespace Modules\User\Services;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\Farmer\Models\Farmer;
use Modules\User\Repositories\Contracts\UserRepositoryInterface;

class UserService extends BaseService
{
    public function __construct(private readonly UserRepositoryInterface $users)
    {
        parent::__construct($users);
    }

    public function listNameOptions(): Collection
    {
        return $this->users->listNameOptions();
    }

    public function availableRoleNames(): SupportCollection
    {
        return $this->users->availableRoleNames();
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $user = $this->repository->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_active' => $data['is_active'] ?? true,
                'region_id' => $data['region_id'] ?? null,
            ]);

            $this->persistRelations($user, $data);

            return $this->repository->findOrFail($user->id, ['roles', 'region', 'farmer']);
        });
    }

    public function update(Model $model, array $data): Model
    {
        return DB::transaction(function () use ($model, $data) {
            $attributes = array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
            ], fn ($value) => ! is_null($value));

            if (array_key_exists('is_active', $data)) {
                $attributes['is_active'] = $data['is_active'];
            }

            if (array_key_exists('region_id', $data)) {
                $attributes['region_id'] = $data['region_id'];
            }

            if (! empty($data['password'])) {
                $attributes['password'] = Hash::make($data['password']);
            }

            if ($attributes !== []) {
                $this->repository->update($model, $attributes);
            }

            $this->persistRelations($model, $data);

            return $this->repository->findOrFail($model->id, ['roles', 'region', 'farmer']);
        });
    }

    private function persistRelations(User $user, array $data): void
    {
        if (isset($data['roles'])) {
            $this->repository->syncRoles($user, $data['roles']);
        }

        $roles = isset($data['roles'])
            ? $data['roles']
            : $user->getRoleNames()->all();

        if (in_array('farmer', $roles, true)) {
            $this->syncFarmerProfile($user, $data);
        }

        if (isset($data['avatar_uuid'])) {
            $user->addMediaFromTemporaryUpload($data['avatar_uuid'], 'avatar');
        }
    }

    /**
     * Keep the farmer role and farmer profile as a usable pair. Partial API
     * updates for an older, unlinked farmer account remain valid; the admin
     * form can repair that account once the required profile fields are sent.
     */
    private function syncFarmerProfile(User $user, array $data): void
    {
        $farmer = $user->farmer;
        $profileData = [
            'name' => $data['name'] ?? $user->name,
        ];

        foreach (['region_id', 'village_id', 'farmer_group_id', 'phone', 'land_area_ha'] as $field) {
            if (array_key_exists($field, $data)) {
                $profileData[$field] = $data[$field];
            }
        }

        if (! $farmer && (! isset($data['region_id']) || ! isset($data['phone']))) {
            return;
        }

        if ($farmer) {
            $farmer->update($profileData);

            if (array_key_exists('commodities', $data)) {
                $farmer->commodities()->sync($data['commodities'] ?? []);
            }

            return;
        }

        $farmer = Farmer::create(array_merge(
            ['user_id' => $user->id],
            $profileData,
        ));

        if (array_key_exists('commodities', $data)) {
            $farmer->commodities()->sync($data['commodities'] ?? []);
        }
    }
}
