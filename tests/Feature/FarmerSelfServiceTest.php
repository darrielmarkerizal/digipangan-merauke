<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function fssSeedRoles(): void
{
    config(['digipangan.admin.email' => null]);
    app(UserDatabaseSeeder::class)->run();
}

function fssRegion(?string $name = null): Region
{
    return Region::create(['name' => $name ?? 'Distrik '.uniqid()]);
}

function fssFarmerUser(?Region $region = null): User
{
    $region ??= fssRegion();
    $user = User::create([
        'name' => 'Petani '.uniqid(),
        'email' => 'petani'.uniqid().'@contoh.test',
        'password' => Hash::make('rahasia123'),
        'is_active' => true,
    ]);
    $user->assignRole('farmer');
    Farmer::create([
        'user_id' => $user->id,
        'region_id' => $region->id,
        'name' => $user->name,
        'phone' => '081200000000',
        'is_active' => true,
    ]);

    return $user->fresh();
}

function fssAdminUser(): User
{
    $user = User::create([
        'name' => 'Admin '.uniqid(),
        'email' => 'admin'.uniqid().'@contoh.test',
        'password' => Hash::make('rahasia123'),
        'is_active' => true,
    ]);
    $user->assignRole('admin');

    return $user->fresh();
}

beforeEach(function () {
    fssSeedRoles();
    $this->withHeader('Origin', config('app.url'));
});

describe('pendaftaran mandiri petani', function () {
    it('membuat akun, profil petani, memberi peran farmer, dan langsung login', function () {
        $region = fssRegion();

        $this->post('/daftar', [
            'name' => 'Budi Tani',
            'email' => 'budi@contoh.test',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'region_id' => $region->id,
            'land_area_ha' => 2.5,
        ])->assertRedirect('/petani/dashboard');

        $this->assertAuthenticated();

        $user = User::where('email', 'budi@contoh.test')->first();
        expect($user)->not->toBeNull()
            ->and($user->hasRole('farmer'))->toBeTrue()
            ->and($user->farmer)->not->toBeNull()
            ->and($user->farmer->region_id)->toBe($region->id)
            ->and((float) $user->farmer->land_area_ha)->toBe(2.5)
            ->and($user->farmer->is_active)->toBeTrue();
    });

    it('mengizinkan kelompok tani dikosongkan', function () {
        $region = fssRegion();

        $this->post('/daftar', [
            'name' => 'Tani Mandiri',
            'email' => 'mandiri@contoh.test',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'region_id' => $region->id,
            'farmer_group_id' => null,
        ])->assertRedirect('/petani/dashboard');

        expect(Farmer::where('name', 'Tani Mandiri')->first()->farmer_group_id)->toBeNull();
    });

    it('menolak kelompok tani dari wilayah lain', function () {
        $region = fssRegion();
        $otherRegion = fssRegion();
        $group = FarmerGroup::create(['region_id' => $otherRegion->id, 'name' => 'Kelompok Luar']);

        $this->post('/daftar', [
            'name' => 'Salah Wilayah',
            'email' => 'salah@contoh.test',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'region_id' => $region->id,
            'farmer_group_id' => $group->id,
        ])->assertSessionHasErrors('farmer_group_id');

        $this->assertGuest();
    });

    it('menolak desa dari wilayah lain', function () {
        $region = fssRegion();
        $otherRegion = fssRegion();
        $village = Village::create(['region_id' => $otherRegion->id, 'name' => 'Desa Luar']);

        $this->post('/daftar', [
            'name' => 'Salah Desa',
            'email' => 'salahdesa@contoh.test',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'region_id' => $region->id,
            'village_id' => $village->id,
        ])->assertSessionHasErrors('village_id');
    });

    it('menolak email yang dipakai akun yang sudah dihapus (soft delete) dengan pesan, bukan 500', function () {
        $region = fssRegion();
        $existing = User::create([
            'name' => 'Lama',
            'email' => 'dipakai@contoh.test',
            'password' => Hash::make('rahasia123'),
            'is_active' => true,
        ]);
        $existing->delete(); // soft delete — baris fisik tetap ada

        $this->post('/daftar', [
            'name' => 'Baru',
            'email' => 'dipakai@contoh.test',
            'phone' => '081234567890',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'region_id' => $region->id,
        ])->assertSessionHasErrors('email');
    });
});

describe('kontrol akses area petani', function () {
    it('mengarahkan tamu ke halaman masuk', function () {
        $this->get('/petani/dashboard')->assertRedirect('/login');
    });

    it('memblokir admin dari area petani', function () {
        $this->actingAs(fssAdminUser())
            ->get('/petani/dashboard')
            ->assertForbidden();
    });

    it('memblokir petani dari area admin', function () {
        $this->actingAs(fssFarmerUser())
            ->get('/admin/dashboard')
            ->assertForbidden();
    });

    it('mengizinkan petani membuka dashboardnya', function () {
        $this->actingAs(fssFarmerUser())
            ->get('/petani/dashboard')
            ->assertOk();
    });
});

describe('produk swalayan petani', function () {
    it('memaksa farmer_id dan region_id dari petani yang login', function () {
        $region = fssRegion();
        $user = fssFarmerUser($region);
        $farmer = $user->farmer;

        $this->actingAs($user)->post('/petani/dashboard/produk', [
            'product_category_id' => ProductCategory::create(['name' => 'Sayur'])->id,
            'unit_id' => Unit::create(['name' => 'Kilogram', 'symbol' => 'kg'])->id,
            'name' => 'Bayam Segar',
            'price' => 5000,
        ])->assertRedirect('/petani/dashboard/produk');

        $product = Product::where('name', 'Bayam Segar')->first();
        expect($product)->not->toBeNull()
            ->and($product->farmer_id)->toBe($farmer->id)
            ->and($product->region_id)->toBe($region->id)
            ->and($product->is_featured)->toBeFalse()
            ->and($product->is_region_featured)->toBeFalse();
    });

    it('mencegah petani mengedit produk milik petani lain', function () {
        $owner = fssFarmerUser();
        $intruder = fssFarmerUser();

        $product = Product::create([
            'product_category_id' => ProductCategory::create(['name' => 'Sayur'])->id,
            'unit_id' => Unit::create(['name' => 'Kilogram', 'symbol' => 'kg'])->id,
            'farmer_id' => $owner->farmer->id,
            'name' => 'Milik Orang',
            'price' => 5000,
            'is_active' => true,
        ]);

        $this->actingAs($intruder)
            ->get("/petani/dashboard/produk/{$product->id}/edit")
            ->assertForbidden();

        $this->actingAs($intruder)
            ->delete("/petani/dashboard/produk/{$product->id}")
            ->assertForbidden();
    });
});

describe('profil swalayan petani', function () {
    it('menyinkronkan nama akun saat nama profil diganti', function () {
        $region = fssRegion();
        $user = fssFarmerUser($region);

        $this->actingAs($user)->put('/petani/dashboard/profil', [
            'name' => 'Nama Baru',
            'phone' => '081299998888',
            'region_id' => $region->id,
        ])->assertRedirect('/petani/dashboard/profil');

        expect($user->fresh()->name)->toBe('Nama Baru')
            ->and($user->farmer->fresh()->name)->toBe('Nama Baru');
    });
});

describe('validasi unggah media', function () {
    beforeEach(fn () => Storage::fake('local'));

    it('menerima berkas gambar', function () {
        $this->actingAs(fssFarmerUser())
            ->post('/admin/media/upload', [
                'file' => UploadedFile::fake()->image('foto.jpg'),
            ])->assertOk()->assertJsonStructure(['folder', 'filename']);
    });

    it('menolak berkas non-gambar dengan 422', function () {
        $this->actingAs(fssFarmerUser())
            ->postJson('/admin/media/upload', [
                'file' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            ])->assertStatus(422)->assertJsonPath('success', false);
    });

    it('menolak unggahan dari tamu', function () {
        $this->post('/admin/media/upload', [
            'file' => UploadedFile::fake()->image('foto.jpg'),
        ])->assertRedirect('/login');
    });
});

describe('pengelolaan foto petani', function () {
    beforeEach(function () {
        Storage::fake('local');
        Storage::fake('public');
    });

    it('menghapus foto profil saat remove_photo dikirim', function () {
        $region = fssRegion();
        $user = fssFarmerUser($region);
        $farmer = $user->farmer;
        $farmer->addMedia(UploadedFile::fake()->image('foto.jpg'))->toMediaCollection('photo');

        expect($farmer->getMedia('photo'))->toHaveCount(1);

        $this->actingAs($user)->put('/petani/dashboard/profil', [
            'name' => $farmer->name,
            'phone' => $farmer->phone,
            'region_id' => $region->id,
            'remove_photo' => true,
        ])->assertRedirect('/petani/dashboard/profil');

        expect($farmer->fresh()->getMedia('photo'))->toHaveCount(0);
    });

    it('mempertahankan urutan galeri produk saat diedit', function () {
        $user = fssFarmerUser();
        $product = Product::create([
            'product_category_id' => ProductCategory::create(['name' => 'Sayur'])->id,
            'unit_id' => Unit::create(['name' => 'Kilogram', 'symbol' => 'kg'])->id,
            'farmer_id' => $user->farmer->id,
            'name' => 'Produk Galeri',
            'price' => 5000,
            'is_active' => true,
        ]);

        $a = $product->addMedia(UploadedFile::fake()->image('a.jpg'))->toMediaCollection('photos');
        $b = $product->addMedia(UploadedFile::fake()->image('b.jpg'))->toMediaCollection('photos');

        expect($product->getMedia('photos')->pluck('id')->all())->toBe([$a->id, $b->id]);

        $this->actingAs($user)->put("/petani/dashboard/produk/{$product->id}", [
            'product_category_id' => $product->product_category_id,
            'unit_id' => $product->unit_id,
            'name' => $product->name,
            'price' => 5000,
            'retained_photos' => [$b->id, $a->id],
        ])->assertRedirect('/petani/dashboard/produk');

        expect($product->fresh()->getMedia('photos')->pluck('id')->all())->toBe([$b->id, $a->id]);
    });
});
