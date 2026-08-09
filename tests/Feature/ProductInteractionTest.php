<?php

use Modules\Farmer\Models\Farmer;
use Modules\Product\Enums\ProductInteractionType;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductInteraction;
use Modules\Product\Models\Unit;
use Modules\Product\Services\ProductInteractionService;
use Modules\Region\Models\Region;

function make_tracked_product(bool $active = true): Product
{
    $region = Region::create(['name' => 'Ulilin '.uniqid()]);
    $farmer = Farmer::create([
        'region_id' => $region->id,
        'name' => 'Petani '.uniqid(),
        'phone' => '+6281234567890',
    ]);

    return Product::create([
        'product_category_id' => ProductCategory::create(['name' => 'Kat '.uniqid()])->id,
        'unit_id' => Unit::create(['name' => 'Sat '.uniqid(), 'symbol' => substr(uniqid(), -8)])->id,
        'farmer_id' => $farmer->id,
        'name' => 'Cabai '.uniqid(),
        'price' => 10000,
        'is_active' => $active,
    ]);
}

beforeEach(function () {
    $this->withHeader('Origin', config('app.url'));
});

describe('ProductInteractionService', function () {
    it('mencatat view sekali dan mendedup pengunjung sama pada hari yang sama', function () {
        $product = make_tracked_product();
        $service = app(ProductInteractionService::class);

        $service->record($product->id, $product->region_id, ProductInteractionType::View, '1.2.3.4', 'UA-Test', 'https://google.com/cari');
        $service->record($product->id, $product->region_id, ProductInteractionType::View, '1.2.3.4', 'UA-Test', null);

        expect(ProductInteraction::where('type', 'view')->count())->toBe(1);
    });

    it('mencatat contact setiap kali dipanggil', function () {
        $product = make_tracked_product();
        $service = app(ProductInteractionService::class);

        $service->record($product->id, $product->region_id, ProductInteractionType::Contact, '1.2.3.4', 'UA-Test', null);
        $service->record($product->id, $product->region_id, ProductInteractionType::Contact, '1.2.3.4', 'UA-Test', null);

        expect(ProductInteraction::where('type', 'contact')->count())->toBe(2);
    });

    it('tidak menyimpan IP mentah dan menyimpan region + referrer host', function () {
        $product = make_tracked_product();

        app(ProductInteractionService::class)
            ->record($product->id, $product->region_id, ProductInteractionType::View, '9.9.9.9', 'UA-Test', 'https://facebook.com/x?y=1');

        $row = ProductInteraction::first();

        expect(strlen($row->visitor_hash))->toBe(64)
            ->and($row->visitor_hash)->not->toContain('9.9.9.9')
            ->and($row->region_id)->toBe($product->region_id)
            ->and($row->referrer_host)->toBe('facebook.com');
    });

    it('membedakan pengunjung berbeda pada produk sama', function () {
        $product = make_tracked_product();
        $service = app(ProductInteractionService::class);

        $service->record($product->id, $product->region_id, ProductInteractionType::View, '1.1.1.1', 'UA-A', null);
        $service->record($product->id, $product->region_id, ProductInteractionType::View, '2.2.2.2', 'UA-B', null);

        expect(ProductInteraction::where('type', 'view')->count())->toBe(2);
    });
});

describe('endpoint tracking publik', function () {
    it('mencatat view via endpoint tanpa auth dan mengembalikan 202', function () {
        $product = make_tracked_product();

        $this->postJson(route('api.product.view', $product))->assertStatus(202);

        expect(ProductInteraction::where('type', 'view')->where('product_id', $product->id)->count())->toBe(1);
    });

    it('mencatat contact via endpoint dan mengembalikan 202', function () {
        $product = make_tracked_product();

        $this->postJson(route('api.product.contact', $product))->assertStatus(202);

        expect(ProductInteraction::where('type', 'contact')->where('product_id', $product->id)->count())->toBe(1);
    });

    it('mengembalikan 404 untuk produk nonaktif', function () {
        $product = make_tracked_product(active: false);

        $this->postJson(route('api.product.view', $product))->assertStatus(404);

        expect(ProductInteraction::count())->toBe(0);
    });

    it('tidak menambah baris view saat pengunjung sama membuka ulang di hari sama', function () {
        $product = make_tracked_product();

        $this->postJson(route('api.product.view', $product))->assertStatus(202);
        $this->postJson(route('api.product.view', $product))->assertStatus(202);

        expect(ProductInteraction::where('type', 'view')->count())->toBe(1);
    });
});

describe('retensi interaksi', function () {
    it('menghapus baris lebih tua dari 24 bulan dan mempertahankan yang baru', function () {
        $product = make_tracked_product();

        ProductInteraction::create([
            'product_id' => $product->id,
            'region_id' => $product->region_id,
            'type' => 'view',
            'occurred_at' => now()->subMonths(25),
        ]);
        ProductInteraction::create([
            'product_id' => $product->id,
            'region_id' => $product->region_id,
            'type' => 'view',
            'occurred_at' => now()->subMonths(1),
        ]);

        $this->artisan('interactions:purge')->assertExitCode(0);

        expect(ProductInteraction::count())->toBe(1);
    });
});
