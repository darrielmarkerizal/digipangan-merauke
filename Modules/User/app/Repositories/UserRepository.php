<?php

namespace Modules\User\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection as SupportCollection;
use Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::query()->with(['roles', 'media', 'region']);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::partial('email'),
            AllowedFilter::exact('is_active'),
            AllowedFilter::exact('region_id'),
            AllowedFilter::callback('role', fn ($query, $value) => $query->whereHas(
                'roles',
                fn ($q) => $q->whereIn('name', (array) $value)
            )),
        ];
    }

    protected function searchable(): array
    {
        return ['name', 'email', 'roles.name'];
    }

    protected function allowedSorts(): array
    {
        return ['name', 'email', 'created_at', 'updated_at'];
    }

    protected function allowedIncludes(): array
    {
        return ['roles', 'media', 'region'];
    }



    public function clearAvatar(User $user): void
    {
        $user->clearMediaCollection('avatar');
    }

    public function syncRoles(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }

    /**
     * Lightweight {id, name} options — used to populate admin "author"
     * pickers without loading full user records.
     */
    public function listNameOptions(): Collection
    {
        return $this->model->newQuery()->select('id', 'name')->get();
    }

    /**
     * All role names known to the permission system, for the admin user
     * form's role picker.
     */
    public function availableRoleNames(): SupportCollection
    {
        return Role::pluck('name');
    }
}
