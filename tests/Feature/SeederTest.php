<?php

use Database\Seeders\DatabaseSeeder;
use Modules\Farmer\Models\Commodity;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Page\Models\Faq;
use Modules\Page\Models\Partner;
use Modules\Page\Models\SiteSetting;
use Modules\Post\Enums\PostStatus;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostCategory;
use Modules\Product\Models\Product;
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

        expect($jumlah['Muting'])->toBe(12)
            ->and($jumlah['Ulilin'])->toBe(11)
            ->and($jumlah['Elikobel'])->toBe(12)
            ->and(Village::count())->toBe(35);
    });

    it('mengisi luas dan populasi seluruh distrik dengan nilai Ulilin terverifikasi', function () {
        expect(Region::whereNotNull('area_km2')->count())->toBe(3)
            ->and(Region::whereNotNull('population')->count())->toBe(3);

        $ulilin = Region::where('name', 'Ulilin')->first();

        expect((float) $ulilin->area_km2)->toBe(3633.08)
            ->and($ulilin->population)->toBe(10791);
    });

    it('mengisi gambaran wilayah dan potensi pertanian tiap distrik', function () {
        Region::all()->each(function (Region $region) {
            expect($region->description)->not->toBeNull()
                ->and($region->agricultural_potential)->not->toBeNull();
        });
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

    it('mengisi latar belakang dan tujuan Tentang dari narasi program', function () {
        expect(SiteSetting::count())->toBe(5)
            ->and(SiteSetting::where('key', 'about_background')->value('value'))->not->toBeNull()
            ->and(SiteSetting::where('key', 'about_purpose')->value('value'))->not->toBeNull();
    });

    it('membiarkan kontak admin kosong untuk diisi pemilik program', function () {
        expect(
            SiteSetting::whereIn('key', ['admin_contact_name', 'admin_contact_phone', 'admin_contact_email'])
                ->whereNotNull('value')
                ->count()
        )->toBe(0);
    });

    it('mengisi lima entri pusat bantuan (FAQ)', function () {
        expect(Faq::count())->toBe(5)
            ->and(Faq::where('is_active', true)->count())->toBe(5);
    });

    it('mengisi peran super_admin, admin, dan farmer', function () {
        expect(Role::pluck('name')->sort()->values()->all())->toBe(['admin', 'farmer', 'super_admin']);
    });
});

describe('data contoh (demo)', function () {
    it('menambah petani contoh termasuk Bapak Muhamad Riam dengan komoditasnya', function () {
        $riam = Farmer::where('name', 'Bapak Muhamad Riam')->first();

        expect($riam)->not->toBeNull()
            ->and($riam->region->name)->toBe('Elikobel')
            ->and($riam->commodities->pluck('name')->all())
            ->toContain('Cabai Rawit', 'Tomat', 'Kopi', 'Ubi Kayu');

        expect(FarmerGroup::where('name', 'Kelompok Tani Elikobel')->exists())->toBeTrue();
    });

    it('menambah produk contoh dengan wilayah terisi otomatis dari petani', function () {
        $product = Product::where('name', 'Cabai Rawit Segar Elikobel')->first();

        expect($product)->not->toBeNull()
            ->and($product->region->name)->toBe('Elikobel')
            ->and($product->is_featured)->toBeTrue()
            ->and(Product::count())->toBeGreaterThanOrEqual(8);
    });

    it('menambah berita contoh yang berstatus terbit', function () {
        expect(Post::where('status', PostStatus::Published)->count())->toBeGreaterThanOrEqual(4);
    });
});

it('aman dijalankan dua kali', function () {
    $farmers = Farmer::count();
    $products = Product::count();

    $this->seed(DatabaseSeeder::class);

    expect(Region::count())->toBe(3)
        ->and(Village::count())->toBe(35)
        ->and(Commodity::count())->toBe(18)
        ->and(Unit::count())->toBe(8)
        ->and(Partner::count())->toBe(2)
        ->and(Faq::count())->toBe(5)
        ->and(Farmer::count())->toBe($farmers)
        ->and(Product::count())->toBe($products);
});
