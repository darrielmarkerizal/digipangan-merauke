<?php

namespace Modules\User\Repositories\Contracts;

use App\Models\User;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection as SupportCollection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function clearAvatar(User $user): void;

    public function syncRoles(User $user, array $roles): void;

    public function listNameOptions(): Collection;

    public function availableRoleNames(): SupportCollection;
}
