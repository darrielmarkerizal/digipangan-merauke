<?php

namespace Modules\Post\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Post\Enums\PostStatus;
use Modules\Post\Repositories\Contracts\PostRepositoryInterface;

class PostService extends BaseService
{
    public function __construct(PostRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $cover = Arr::pull($data, 'cover');
        $data['author_id'] = Auth::id();
        $data = $this->applyPublishState($data);

        return DB::transaction(function () use ($data, $cover) {
            $post = $this->repository->create($data);

            if ($cover) {
                $post->addMediaFromTemporaryUpload($cover, 'cover');
            }

            return $this->repository->findOrFail($post->getKey());
        });
    }

    public function update(Model $model, array $data): Model
    {
        $cover = Arr::pull($data, 'cover');
        $data = $this->applyPublishState($data);

        return DB::transaction(function () use ($model, $data, $cover) {
            $post = $this->repository->update($model, $data);

            if ($cover) {
                $post->addMediaFromTemporaryUpload($cover, 'cover');
            }

            return $this->repository->findOrFail($post->getKey());
        });
    }

    private function applyPublishState(array $data): array
    {
        if (($data['status'] ?? null) === PostStatus::Published->value && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
