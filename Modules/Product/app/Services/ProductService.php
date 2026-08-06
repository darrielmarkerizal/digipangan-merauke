<?php

namespace Modules\Product\Services;

use App\Services\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Product\Repositories\Contracts\ProductRepositoryInterface;

class ProductService extends BaseService
{
    public function __construct(private readonly ProductRepositoryInterface $products)
    {
        parent::__construct($products);
    }

    public function publicFeatured(int $limit = 8): Collection
    {
        return $this->products->publicFeatured($limit);
    }

    public function publicLatest(int $limit = 8): Collection
    {
        return $this->products->publicLatest($limit);
    }

    public function publicRelated(Model $product, int $limit = 4): Collection
    {
        return $this->products->publicRelated($product->id, $product->product_category_id, $product->region_id, $limit);
    }

    public function create(array $data): Model
    {
        $photos = Arr::pull($data, 'photos', []);

        return DB::transaction(function () use ($data, $photos) {
            $product = $this->repository->create($data);
            $this->syncPhotos($product, $photos, null);

            return $this->repository->findOrFail($product->getKey());
        });
    }

    public function update(Model $model, array $data): Model
    {
        $photos = Arr::pull($data, 'photos', null);
        $retained = Arr::pull($data, 'retained_photos', null);

        return DB::transaction(function () use ($model, $data, $photos, $retained) {
            $product = $this->repository->update($model, $data);
            $this->syncPhotos($product, $photos, $retained);

            return $this->repository->findOrFail($product->getKey());
        });
    }

    /**
     * Sync the `photos` collection. When $retainedIds is an array, existing
     * media not listed are removed; when null, existing media are kept.
     * New temporary uploads in $newFolders are always appended.
     */
    private function syncPhotos(Model $product, ?array $newFolders, ?array $retainedIds): void
    {
        if (is_array($retainedIds)) {
            $product->getMedia('photos')
                ->reject(fn ($media) => in_array($media->id, $retainedIds))
                ->each(fn ($media) => $media->delete());
        }

        foreach ($newFolders ?? [] as $folder) {
            $product->addMediaFromTemporaryUpload($folder, 'photos');
        }
    }
}
