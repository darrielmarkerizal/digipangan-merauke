<?php

namespace Modules\Post\Services;

use App\Services\BaseService;
use App\Support\PostContentSanitizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Post\Enums\PostStatus;
use Modules\Post\Repositories\Contracts\PostRepositoryInterface;

class PostService extends BaseService
{
    public function __construct(
        PostRepositoryInterface $repository,
        private readonly PostContentSanitizer $contentSanitizer,
    )
    {
        parent::__construct($repository);
    }

    public function create(array $data): Model
    {
        $cover = Arr::pull($data, 'cover');
        $contentMedia = Arr::pull($data, 'content_media', []);
        $data['author_id'] = Auth::id();
        $data = $this->applyPublishState($data);

        return DB::transaction(function () use ($data, $cover, $contentMedia) {
            $post = $this->repository->create($data);

            if ($cover) {
                $post->addMediaFromTemporaryUpload($cover, 'cover');
            }

            $body = $this->replaceContentMediaMarkers($post, $data['body'], $contentMedia);
            $body = $this->contentSanitizer->sanitize($body);
            if ($body !== $data['body']) {
                $this->repository->update($post, ['body' => $body]);
            }

            return $this->repository->findOrFail($post->getKey());
        });
    }

    public function update(Model $model, array $data): Model
    {
        $cover = Arr::pull($data, 'cover');
        $contentMedia = Arr::pull($data, 'content_media', []);
        $data = $this->applyPublishState($data);

        return DB::transaction(function () use ($model, $data, $cover, $contentMedia) {
            $post = $this->repository->update($model, $data);

            if ($cover) {
                $post->addMediaFromTemporaryUpload($cover, 'cover');
            }

            $body = $this->replaceContentMediaMarkers($model, $data['body'], $contentMedia);
            $body = $this->contentSanitizer->sanitize($body);
            if ($body !== $data['body']) {
                $this->repository->update($model, ['body' => $body]);
            }

            return $this->repository->findOrFail($post->getKey());
        });
    }

    private function replaceContentMediaMarkers(Model $post, string $body, array $folders): string
    {
        foreach (array_values(array_unique($folders)) as $folder) {
            $media = $post->addMediaFromTemporaryUpload($folder, 'content_media');

            if (! $media) {
                continue;
            }

            $url = e($media->getUrl());
            $replacement = str_starts_with((string) $media->mime_type, 'video/')
                ? '<video controls preload="metadata" class="post-content-video" src="'.$url.'"></video>'
                : '<img loading="lazy" class="post-content-image" src="'.$url.'" alt="Media berita">';

            $folderPattern = preg_quote($folder, '~');
            $body = preg_replace(
                '~<p\b[^>]*data-temp-media=["\']'.$folderPattern.'["\'][^>]*>.*?</p>~is',
                $replacement,
                $body
            ) ?? $body;
        }

        return $body;
    }

    private function applyPublishState(array $data): array
    {
        if (($data['status'] ?? null) === PostStatus::Published->value && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
