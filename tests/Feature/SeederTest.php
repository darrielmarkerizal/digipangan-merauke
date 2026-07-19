<?php

use Database\Seeders\DatabaseSeeder;
use Modules\Farmer\Models\Commodity;
use Modules\Page\Models\Partner;
use Modules\Page\Models\SiteSetting;
use Modules\Post\Models\PostCategory;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;
use Spatie\Permission\Models\Role;

beforeEach(fn () => $this->seed(DatabaseSeeder::class));

describe('wilayah dan kampung', function () {
    it('mengisi tiga distrik transmigrasi', function () {
        expect(Region::pluck('name')->all())->toBe(['Muting', 'Ulilin', 'Elikobel']);
    });

    it('mengisi jumlah kampung sesuai data per distrik', function () {
        $jumlah = Region::withCount('villages')->pluck('villages_count', 'name');

        expect($jumlah['Muting'])->toBe(11)
            ->and($jumlah['Ulilin'])->toBe(11)
            ->and($jumlah['Elikobel'])->toBe(12)
            ->and(Village::count())->toBe(34);
    });

    it('hanya mengisi statistik BPS untuk Ulilin', function () {
        $ulilin = Region::where('name', 'Ulilin')->first();

        expect((float) $ulilin->area_km2)->toBe(3633.08)
            ->and($ulilin->population)->toBe(10791)
            ->and(Region::whereNotNull('population')->count())->toBe(1);
    });

    it('membuat slug unik untuk seluruh kampung', function () {
        expect(Village::distinct()->count('slug'))->toBe(Village::count());
    });
});

describe('master data', function () {
    it('mengisi 18 komoditas dari kedua sumber di PDF', function () {
        expect(Commodity::count())->toBe(18)
            ->and(Commodity::pluck('name'))
            ->toContain('Padi', 'Karet')
            ->toContain('Bawang Merah', 'Bawang Putih', 'Kentang', 'Sawi')
            ->toContain('Cabai Besar', 'Cabai Keriting', 'Cabai Rawit');
    });

    it('tidak menyimpan cabai sebagai satu entri gabungan', function () {
        expect(Commodity::where('name', 'Cabai')->exists())->toBeFalse();
    });

    it('mengisi lima kategori produk sesuai filter halaman Produk', function () {
        expect(ProductCategory::orderBy('sort_order')->pluck('name')->all())
            ->toBe(['Sayuran', 'Buah-buahan', 'Hasil Perkebunan', 'Peternakan', 'Olahan Pangan']);
    });

    it('mengisi empat kategori berita', function () {
        expect(PostCategory::count())->toBe(4);
    });

    it('mengisi delapan satuan dengan simbol unik', function () {
        expect(Unit::count())->toBe(8)
            ->and(Unit::distinct()->count('symbol'))->toBe(8);
    });
});

describe('halaman tentang dan peran', function () {
    it('mengisi dua institusi pendukung', function () {
        expect(Partner::pluck('name')->all())
            ->toBe(['Universitas Gadjah Mada', 'Kementerian Transmigrasi RI']);
    });

    it('menyiapkan lima key pengaturan tanpa mengarang isinya', function () {
        expect(SiteSetting::count())->toBe(5)
            ->and(SiteSetting::whereNotNull('value')->count())->toBe(0);
    });

    it('mengisi peran super_admin dan admin', function () {
        expect(Role::pluck('name')->sort()->values()->all())->toBe(['admin', 'super_admin']);
    });
});

it('aman dijalankan dua kali', function () {
    $this->seed(DatabaseSeeder::class);

    expect(Region::count())->toBe(3)
        ->and(Village::count())->toBe(34)
        ->and(Commodity::count())->toBe(18)
        ->and(Unit::count())->toBe(8)
        ->and(Partner::count())->toBe(2);
});
