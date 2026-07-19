<?php

namespace Modules\User\Repositories;

use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Http\UploadedFile;
use Modules\User\Repositories\Contracts\UserRepositoryInterface;
use Spatie\QueryBuilder\AllowedFilter;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    protected function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::partial('email'),
            AllowedFilter::exact('is_active'),
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
        return ['roles', 'media'];
    }

    public function replaceAvatar(User $user, UploadedFile $file): void
    {
        $user->addMedia($file)->toMediaCollection('avatar');
    }

    public function clearAvatar(User $user): void
    {
        $user->clearMediaCollection('avatar');
    }

    public function syncRoles(User $user, array $roles): void
    {
        $user->syncRoles($roles);
    }
}
