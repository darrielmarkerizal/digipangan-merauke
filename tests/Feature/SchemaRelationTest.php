<?php

use Illuminate\Database\QueryException;
use Modules\Farmer\Models\Commodity;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Product\Enums\ProductInteractionType;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductInteraction;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;

function makeRegion(string $name = 'Ulilin'): Region
{
    return Region::create(['name' => $name]);
}

function makeFarmer(?Region $region = null): Farmer
{
    $region ??= makeRegion();
    $village = Village::create(['region_id' => $region->id, 'name' => 'Selil']);
    $group = FarmerGroup::create(['region_id' => $region->id, 'village_id' => $village->id, 'name' => 'Kelompok Tani Elikobel']);

    return Farmer::create([
        'region_id' => $region->id,
        'village_id' => $village->id,
        'farmer_group_id' => $group->id,
        'name' => 'Muhamad Riam',
        'phone' => '+6281234567890',
        'land_area_ha' => 2.5,
    ]);
}

function makeProduct(?Farmer $farmer = null, array $overrides = []): Product
{
    $farmer ??= makeFarmer();

    return Product::create(array_merge([
        'product_category_id' => ProductCategory::create(['name' => 'Sayuran'])->id,
        'unit_id' => Unit::create(['name' => 'Kilogram', 'symbol' => 'kg'])->id,
        'farmer_id' => $farmer->id,
        'name' => 'Cabai Rawit Segar',
        'price' => 45000,
    ], $overrides));
}

describe('relasi antar modul', function () {
    it('menghubungkan wilayah ke desa, kelompok tani, petani, dan produk', function () {
        $product = makeProduct();
        $region = $product->region;

        expect($region->villages)->toHaveCount(1)
            ->and($region->farmerGroups)->toHaveCount(1)
            ->and($region->farmers)->toHaveCount(1)
            ->and($region->products)->toHaveCount(1);
    });

    it('menghubungkan produk ke kategori, satuan, petani, dan wilayah', function () {
        $product = makeProduct();

        expect($product->category->name)->toBe('Sayuran')
            ->and($product->unit->symbol)->toBe('kg')
            ->and($product->farmer->name)->toBe('Muhamad Riam')
            ->and($product->region->name)->toBe('Ulilin');
    });

    it('menghitung desa dan kelompok tani tanpa kolom penghitung', function () {
        makeFarmer();

        $region = Region::withCount(['villages', 'farmerGroups'])->first();

        expect($region->villages_count)->toBe(1)
            ->and($region->farmer_groups_count)->toBe(1)
            ->and($region->getAttributes())->not->toHaveKey('village_count');
    });

    it('memasangkan petani dengan banyak komoditas lewat pivot', function () {
        $farmer = makeFarmer();
        $farmer->commodities()->attach([
            Commodity::create(['name' => 'Cabai Rawit'])->id,
            Commodity::create(['name' => 'Tomat'])->id,
        ]);

        expect($farmer->commodities)->toHaveCount(2)
            ->and(Commodity::first()->farmers)->toHaveCount(1);
    });
});

describe('slug otomatis', function () {
    it('membuat slug dari nama saat model disimpan', function () {
        expect(makeRegion('Elikobel')->slug)->toBe('elikobel')
            ->and(makeProduct()->slug)->toBe('cabai-rawit-segar');
    });

    it('menjaga slug tetap unik ketika nama sama', function () {
        makeRegion('Muting');
        $kedua = makeRegion('Muting');

        expect($kedua->slug)->not->toBe('muting')
            ->and($kedua->slug)->toStartWith('muting');
    });

    it('memakai slug sebagai kunci rute', function () {
        expect(makeRegion()->getRouteKeyName())->toBe('slug');
    });
});

describe('sinkronisasi region_id pada produk', function () {
    it('mengisi region_id dari petani meski tidak diisi manual', function () {
        $farmer = makeFarmer();

        expect(makeProduct($farmer)->region_id)->toBe($farmer->region_id);
    });

    it('memperbarui region_id ketika produk berpindah petani', function () {
        $product = makeProduct();
        $lain = makeFarmer(makeRegion('Muting'));

        $product->update(['farmer_id' => $lain->id]);

        expect($product->fresh()->region_id)->toBe($lain->region_id)
            ->and($product->fresh()->region->name)->toBe('Muting');
    });
});

describe('integritas referensial', function () {
    it('menolak menghapus wilayah yang masih dirujuk produk', function () {
        $region = makeProduct()->region;

        expect(fn () => $region->forceDelete())->toThrow(QueryException::class);
    });

    it('menolak menghapus satuan yang masih dipakai produk', function () {
        $unit = makeProduct()->unit;

        expect(fn () => $unit->delete())->toThrow(QueryException::class);
    });

    it('ikut menghapus interaksi ketika produk dihapus permanen', function () {
        $product = makeProduct();
        ProductInteraction::create([
            'product_id' => $product->id,
            'region_id' => $product->region_id,
            'type' => ProductInteractionType::Contact,
            'occurred_at' => now(),
        ]);

        $product->forceDelete();

        expect(ProductInteraction::count())->toBe(0);
    });
});

describe('soft delete', function () {
    it('menyembunyikan produk terhapus dari query biasa', function () {
        makeProduct()->delete();

        expect(Product::count())->toBe(0)
            ->and(Product::withTrashed()->count())->toBe(1);
    });

    it('mempertahankan produk milik petani yang di-soft-delete', function () {
        $product = makeProduct();
        $product->farmer->delete();

        expect(Product::count())->toBe(1)
            ->and($product->fresh()->farmer)->toBeNull();
    });
});

describe('audit', function () {
    it('merekam perubahan pada model yang diaudit', function () {
        config(['audit.console' => true]);
        $product = makeProduct();

        $product->update(['price' => 50000]);
        $audit = $product->audits()->where('event', 'updated')->first();

        expect($audit)->not->toBeNull()
            ->and($audit->old_values['price'])->toEqual(45000)
            ->and($audit->new_values['price'])->toEqual(50000);
    });
});
