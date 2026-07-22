<?php

namespace Modules\Post\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Modules\Post\Http\Resources\Public\PublicPostDetailResource;
use Modules\Post\Http\Resources\Public\PublicPostResource;
use Modules\Post\Repositories\Contracts\PostRepositoryInterface;

class PostController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PostRepositoryInterface $posts) {}

    public function index(): JsonResponse
    {
        $paginator = $this->posts->publicPaginateFiltered();

        return $this->paginatedResponse(
            $paginator->setCollection(PublicPostResource::collection($paginator->getCollection())->collection)
        );
    }

    public function show(string $slug): JsonResponse
    {
        $post = $this->posts->publicFindBySlug($slug);

        abort_if($post === null, 404, 'Berita tidak ditemukan.');

        return $this->successResponse(new PublicPostDetailResource($post));
    }
}
