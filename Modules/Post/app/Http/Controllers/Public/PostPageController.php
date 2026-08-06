<?php

namespace Modules\Post\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Post\Http\Resources\Public\PublicPostDetailResource;
use Modules\Post\Http\Resources\Public\PublicPostResource;
use Modules\Post\Models\PostCategory;
use Modules\Post\Repositories\Contracts\PostCategoryRepositoryInterface;
use Modules\Post\Repositories\Contracts\PostRepositoryInterface;

class PostPageController extends Controller
{
    public function __construct(
        private readonly PostRepositoryInterface $posts,
        private readonly PostCategoryRepositoryInterface $categories,
    ) {}

    public function index(Request $request): Response
    {
        $paginator = $this->posts->publicPaginateFiltered();

        $categories = $this->categories->withPublicPostsCount()
            ->map(fn (PostCategory $category) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'posts_count' => (int) $category->posts_count,
            ]);

        return Inertia::render('Post/Index', [
            'posts' => PublicPostResource::collection($paginator),
            'categories' => $categories,
            'filters' => (object) $request->only(['kategori', 'q']),
        ]);
    }

    public function show(string $slug): Response
    {
        $post = $this->posts->publicFindBySlug($slug);

        abort_if($post === null, 404, 'Berita tidak ditemukan.');

        $relatedPosts = $this->posts->publicRelated($post->id, $post->post_category_id, 3);

        return Inertia::render('Post/Show', [
            'post' => (new PublicPostDetailResource($post))->resolve(),
            'relatedPosts' => PublicPostResource::collection($relatedPosts)->resolve(),
        ]);
    }
}
