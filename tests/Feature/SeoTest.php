<?php

use App\Models\User;
use App\Support\PublicUrl;
use Modules\Farmer\Models\Farmer;
use Modules\Post\Enums\PostStatus;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostCategory;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;

function seo_graph(): array
{
    $region = Region::create(['name' => 'Ulilin', 'description' => 'Distrik Ulilin.']);
    $farmer = Farmer::create(['region_id' => $region->id, 'name' => 'Bapak Muhamad Riam', 'phone' => '+6281234567890']);
    $product = Product::create([
        'product_category_id' => ProductCategory::create(['name' => 'Sayuran'])->id,
        'unit_id' => Unit::create(['name' => 'Kilogram', 'symbol' => 'kg'])->id,
        'farmer_id' => $farmer->id,
        'name' => 'Cabai Rawit Segar',
        'description' => 'Cabai segar dari Elikobel.',
        'price' => 45000,
        'is_active' => true,
    ]);
    $author = User::factory()->create(['is_active' => true]);
    $post = Post::create([
        'post_category_id' => PostCategory::create(['name' => 'Berita Panen'])->id,
        'author_id' => $author->id,
        'title' => 'Panen Raya Cabai',
        'body' => 'Isi berita panen raya.',
        'status' => PostStatus::Published,
        'published_at' => now(),
    ]);

    return compact('region', 'farmer', 'product', 'post');
}

beforeEach(fn () => $this->withHeader('Origin', config('app.url')));

describe('sitemap.xml', function () {
    it('menyajikan XML berisi halaman publik aktif dan mengecualikan yang tersembunyi', function () {
        ['product' => $product, 'region' => $region, 'farmer' => $farmer, 'post' => $post] = seo_graph();

        $hidden = Product::create([
            'product_category_id' => $product->product_category_id,
            'unit_id' => $product->unit_id,
            'farmer_id' => $product->farmer_id,
            'name' => 'Produk Nonaktif',
            'price' => 1000,
            'is_active' => false,
        ]);
        $draft = Post::create([
            'post_category_id' => $post->post_category_id,
            'author_id' => $post->author_id,
            'title' => 'Draf Belum Terbit',
            'body' => 'x',
            'status' => PostStatus::Draft,
        ]);

        $response = $this->get('/sitemap.xml')->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $response->assertSee(PublicUrl::products(), false)
            ->assertSee(PublicUrl::posts(), false)
            ->assertSee(PublicUrl::about(), false)
            ->assertSee(PublicUrl::product($product->slug), false)
            ->assertSee(PublicUrl::region($region->slug), false)
            ->assertSee(PublicUrl::farmer($farmer->slug), false)
            ->assertSee(PublicUrl::post($post->slug), false);

        $response->assertDontSee(PublicUrl::product($hidden->slug), false)
            ->assertDontSee(PublicUrl::post($draft->slug), false);
    });
});

describe('metadata SEO pada endpoint publik', function () {
    it('menyertakan data terstruktur schema.org/Product pada detail produk', function () {
        $product = seo_graph()['product'];

        $this->getJson(route('api.public.product.show', $product->slug))
            ->assertOk()
            ->assertJsonPath('data.seo.canonical', PublicUrl::product($product->slug))
            ->assertJsonPath('data.seo.structured_data.@type', 'Product')
            ->assertJsonPath('data.seo.structured_data.offers.priceCurrency', 'IDR')
            ->assertJsonPath('data.seo.structured_data.offers.availability', 'https://schema.org/InStock');
    });

    it('menyertakan data terstruktur schema.org/Article pada detail berita', function () {
        $post = seo_graph()['post'];

        $this->getJson(route('api.public.post.show', $post->slug))
            ->assertOk()
            ->assertJsonPath('data.seo.canonical', PublicUrl::post($post->slug))
            ->assertJsonPath('data.seo.structured_data.@type', 'Article')
            ->assertJsonPath('data.seo.structured_data.headline', 'Panen Raya Cabai');
    });

    it('menyertakan judul dan canonical unik pada wilayah dan petani', function () {
        ['region' => $region, 'farmer' => $farmer] = seo_graph();

        $this->getJson(route('api.public.region.show', $region->slug))
            ->assertOk()
            ->assertJsonPath('data.seo.canonical', PublicUrl::region($region->slug))
            ->assertJsonPath('data.seo.title', fn ($title) => str_contains($title, 'Ulilin'));

        $this->getJson(route('api.public.farmer.show', $farmer->slug))
            ->assertOk()
            ->assertJsonPath('data.seo.canonical', PublicUrl::farmer($farmer->slug))
            ->assertJsonPath('data.seo.title', fn ($title) => str_contains($title, 'Muhamad Riam'));
    });
});
