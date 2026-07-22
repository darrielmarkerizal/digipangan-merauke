<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Farmer\Models\Farmer;
use Modules\Media\Services\TemporaryMediaService;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_product(string $role = 'super_admin'): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

function product_prereqs(): array
{
    $region = Region::create(['name' => 'Ulilin '.uniqid()]);
    $farmer = Farmer::create([
        'region_id' => $region->id,
        'name' => 'Petani '.uniqid(),
        'phone' => '+6281234567890',
    ]);

    return [
        'region' => $region,
        'farmer' => $farmer,
        'category' => ProductCategory::create(['name' => 'Sayuran '.uniqid()]),
        'unit' => Unit::create(['name' => 'Kilogram '.uniqid(), 'symbol' => substr(uniqid(), -8)]),
    ];
}

function product_payload(array $prereqs, array $overrides = []): array
{
    return array_merge([
        'product_category_id' => $prereqs['category']->id,
        'unit_id' => $prereqs['unit']->id,
        'farmer_id' => $prereqs['farmer']->id,
        'name' => 'Cabai Rawit',
        'price' => 45000,
    ], $overrides);
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Product CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.product.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_product('admin'))->getJson(route('api.product.index'))->assertOk();
    });

    it('menolak pengguna tanpa izin kelola produk dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.product.index'))->assertStatus(403);
    });

    it('membuat produk dan mengisi region_id otomatis dari petani', function () {
        $prereqs = product_prereqs();

        $this->actingAs(actor_product())
            ->postJson(route('api.product.store'), product_payload($prereqs))
            ->assertCreated()
            ->assertJsonPath('data.name', 'Cabai Rawit')
            ->assertJsonPath('data.region.id', $prereqs['region']->id);

        $this->assertDatabaseHas('products', [
            'name' => 'Cabai Rawit',
            'farmer_id' => $prereqs['farmer']->id,
            'region_id' => $prereqs['region']->id,
        ]);
    });

    it('menolak petani yang tidak ada dengan 422', function () {
        $prereqs = product_prereqs();

        $this->actingAs(actor_product())
            ->postJson(route('api.product.store'), product_payload($prereqs, ['farmer_id' => 999999]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('farmer_id');
    });

    it('menolak harga negatif dengan 422', function () {
        $prereqs = product_prereqs();

        $this->actingAs(actor_product())
            ->postJson(route('api.product.store'), product_payload($prereqs, ['price' => -1]))
            ->assertStatus(422)
            ->assertJsonValidationErrors('price');
    });

    it('memperbarui produk', function () {
        $prereqs = product_prereqs();
        $product = Product::create(product_payload($prereqs));

        $this->actingAs(actor_product())
            ->putJson(route('api.product.update', $product->id), product_payload($prereqs, ['name' => 'Cabai Merah']))
            ->assertOk()
            ->assertJsonPath('data.name', 'Cabai Merah');
    });

    it('melampirkan foto dari unggahan sementara', function () {
        Storage::fake('local');
        Storage::fake('public');

        $prereqs = product_prereqs();
        $folder = app(TemporaryMediaService::class)
            ->handleUpload(UploadedFile::fake()->image('cabai.jpg', 600, 600))
            ->folder;

        $this->actingAs(actor_product())
            ->postJson(route('api.product.store'), product_payload($prereqs, ['photos' => [$folder]]))
            ->assertCreated()
            ->assertJsonCount(1, 'data.photos');

        expect(Product::first()->getMedia('photos'))->toHaveCount(1);
    });

    it('menyinkronkan foto: menghapus yang tidak dipertahankan lalu menambah baru', function () {
        Storage::fake('local');
        Storage::fake('public');

        $prereqs = product_prereqs();
        $service = app(TemporaryMediaService::class);

        $product = Product::create(product_payload($prereqs));
        $product->addMediaFromTemporaryUpload(
            $service->handleUpload(UploadedFile::fake()->image('a.jpg', 400, 400))->folder,
            'photos'
        );
        $product->addMediaFromTemporaryUpload(
            $service->handleUpload(UploadedFile::fake()->image('b.jpg', 400, 400))->folder,
            'photos'
        );
        $keepId = $product->getMedia('photos')->first()->id;
        $dropId = $product->getMedia('photos')->last()->id;

        $newFolder = $service->handleUpload(UploadedFile::fake()->image('c.jpg', 400, 400))->folder;

        $this->actingAs(actor_product())
            ->putJson(route('api.product.update', $product->id), product_payload($prereqs, [
                'retained_photos' => [$keepId],
                'photos' => [$newFolder],
            ]))
            ->assertOk()
            ->assertJsonCount(2, 'data.photos');

        $ids = $product->fresh()->getMedia('photos')->pluck('id');
        expect($ids)->toContain($keepId)
            ->and($ids)->not->toContain($dropId)
            ->and($ids)->toHaveCount(2);
    });

    it('menghapus produk (soft delete)', function () {
        $prereqs = product_prereqs();
        $product = Product::create(product_payload($prereqs));

        $this->actingAs(actor_product())
            ->deleteJson(route('api.product.destroy', $product->id))
            ->assertOk();

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    });
});
