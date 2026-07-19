<?php

namespace Modules\User\Repositories\Contracts;

use App\Models\User;
use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Http\UploadedFile;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function replaceAvatar(User $user, UploadedFile $file): void;

    public function clearAvatar(User $user): void;

    public function syncRoles(User $user, array $roles): void;
}
