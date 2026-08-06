<?php

namespace Modules\User\Services;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            ]);

            $this->persistRelations($user, $data);

            return $this->repository->findOrFail($user->id, ['roles']);
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

            if (! empty($data['password'])) {
                $attributes['password'] = Hash::make($data['password']);
            }

            if ($attributes !== []) {
                $this->repository->update($model, $attributes);
            }

            $this->persistRelations($model, $data);

            return $this->repository->findOrFail($model->id, ['roles']);
        });
    }

    private function persistRelations(User $user, array $data): void
    {
        if (isset($data['roles'])) {
            $this->repository->syncRoles($user, $data['roles']);
        }

        if (isset($data['avatar_uuid'])) {
            $user->addMediaFromTemporaryUpload($data['avatar_uuid'], 'avatar');
        }
    }
}
