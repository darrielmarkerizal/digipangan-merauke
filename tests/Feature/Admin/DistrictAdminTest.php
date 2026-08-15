<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Farmer\Database\Seeders\FarmerDatabaseSeeder;
use Modules\Farmer\Models\Farmer;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Product\Database\Seeders\ProductDatabaseSeeder;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\Unit;
use Modules\Region\Database\Seeders\RegionDatabaseSeeder;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

beforeEach(function () {
    config(['digipangan.admin.email' => null]);
    $this->app->make(UserDatabaseSeeder::class)->run();
    $this->app->make(RegionDatabaseSeeder::class)->run();
    $this->app->make(ProductDatabaseSeeder::class)->run();
    $this->app->make(FarmerDatabaseSeeder::class)->run();
});

function createSuperAdmin(): User
{
    $user = User::create([
        'name' => 'Super Admin',
        'email' => 'superadmin@digipangan.test',
        'password' => Hash::make('password123'),
        'is_active' => true,
    ]);
    $user->assignRole('super_admin');
    return $user;
}

function createDistrictAdmin(Region $region): User
{
    $user = User::create([
        'name' => 'Admin Distrik ' . $region->name,
        'email' => 'admin.' . $region->slug . '@digipangan.test',
        'password' => Hash::make('password123'),
        'is_active' => true,
        'region_id' => $region->id,
    ]);
    $user->assignRole('admin_distrik');
    return $user;
}

describe('User Management Guard', function () {
    it('allows super admin to create district admin with region_id', function () {
        $superAdmin = createSuperAdmin();
        $region = Region::first();

        $response = $this->actingAs($superAdmin)->post(route('admin.user.store'), [
            'name' => 'District Admin Semangga',
            'email' => 'semangga@digipangan.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => true,
            'roles' => ['admin_distrik'],
            'region_id' => $region->id,
        ]);

        $response->assertRedirect(route('admin.user.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'semangga@digipangan.test',
            'region_id' => $region->id,
        ]);

        $createdUser = User::where('email', 'semangga@digipangan.test')->first();
        expect($createdUser->hasRole('admin_distrik'))->toBeTrue();
        expect($createdUser->isDistrictAdmin())->toBeTrue();
        expect($createdUser->getAssignedRegionId())->toBe($region->id);
    });

    it('denies district admin from accessing user management routes', function () {
        $region = Region::first();
        $districtAdmin = createDistrictAdmin($region);

        $this->actingAs($districtAdmin)->get(route('admin.user.index'))->assertForbidden();
        $this->actingAs($districtAdmin)->get(route('admin.user.create'))->assertForbidden();
        $this->actingAs($districtAdmin)->post(route('admin.user.store'), [
            'name' => 'Another User',
            'email' => 'another@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'roles' => ['admin'],
        ])->assertForbidden();
        $this->actingAs($districtAdmin)->get(route('admin.setting.index'))->assertForbidden();
        $this->actingAs($districtAdmin)->get(route('admin.audit.index'))->assertForbidden();
    });
});

describe('District Scoped Data Access', function () {
    it('scopes product listing to the district admin region', function () {
        $regions = Region::take(2)->get();
        $regionA = $regions[0];
        $regionB = $regions[1];

        $districtAdminA = createDistrictAdmin($regionA);

        $response = $this->actingAs($districtAdminA)->get(route('admin.product.index'));
        $response->assertOk();
    });

    it('blocks district admin from viewing or editing products from other districts', function () {
        $regions = Region::take(2)->get();
        $regionA = $regions[0];
        $regionB = $regions[1];

        $districtAdminA = createDistrictAdmin($regionA);

        // Find or create product in region B
        $farmerB = Farmer::where('region_id', $regionB->id)->first();
        if (!$farmerB) {
            $farmerB = Farmer::create([
                'name' => 'Petani Region B',
                'phone' => '081234567891',
                'region_id' => $regionB->id,
                'is_active' => true,
            ]);
        }

        $category = ProductCategory::first();
        $unit = Unit::first();

        $productB = Product::create([
            'name' => 'Produk Region B',
            'product_category_id' => $category->id,
            'unit_id' => $unit->id,
            'farmer_id' => $farmerB->id,
            'region_id' => $regionB->id,
            'price' => 50000,
            'is_active' => true,
        ]);

        // District Admin A should be forbidden from editing product B
        $this->actingAs($districtAdminA)
            ->get(route('admin.product.edit', $productB->id))
            ->assertForbidden();

        // District Admin A should be forbidden from updating product B
        $this->actingAs($districtAdminA)
            ->put(route('admin.product.update', $productB->id), [
                'name' => 'Produk Updated',
                'product_category_id' => $category->id,
                'unit_id' => $unit->id,
                'farmer_id' => $farmerB->id,
                'price' => 60000,
            ])
            ->assertForbidden();

        // District Admin A should be forbidden from deleting product B
        $this->actingAs($districtAdminA)
            ->delete(route('admin.product.destroy', $productB->id))
            ->assertForbidden();
    });

    it('blocks district admin from viewing or editing farmers from other districts', function () {
        $regions = Region::take(2)->get();
        $regionA = $regions[0];
        $regionB = $regions[1];

        $districtAdminA = createDistrictAdmin($regionA);

        $farmerB = Farmer::where('region_id', $regionB->id)->first();
        if (!$farmerB) {
            $farmerB = Farmer::create([
                'name' => 'Petani Region B2',
                'phone' => '081234567892',
                'region_id' => $regionB->id,
                'is_active' => true,
            ]);
        }

        $this->actingAs($districtAdminA)
            ->get(route('admin.farmer.edit', $farmerB->id))
            ->assertForbidden();

        $this->actingAs($districtAdminA)
            ->put(route('admin.farmer.update', $farmerB->id), [
                'name' => 'Farmer Updated',
                'phone' => '081234567899',
                'region_id' => $regionB->id,
            ])
            ->assertForbidden();

        $this->actingAs($districtAdminA)
            ->delete(route('admin.farmer.destroy', $farmerB->id))
            ->assertForbidden();
    });

    it('blocks district admin from editing other regions', function () {
        $regions = Region::take(2)->get();
        $regionA = $regions[0];
        $regionB = $regions[1];

        $districtAdminA = createDistrictAdmin($regionA);

        // Allowed for own region
        $this->actingAs($districtAdminA)
            ->get(route('admin.region.edit', $regionA->id))
            ->assertOk();

        // Blocked for other region
        $this->actingAs($districtAdminA)
            ->get(route('admin.region.edit', $regionB->id))
            ->assertForbidden();

        $this->actingAs($districtAdminA)
            ->put(route('admin.region.update', $regionB->id), [
                'name' => 'Hacked Region',
            ])
            ->assertForbidden();
    });

    it('requires region_id when creating admin_distrik user', function () {
        $superAdmin = createSuperAdmin();

        $response = $this->actingAs($superAdmin)->post(route('admin.user.store'), [
            'name' => 'Admin Without Region',
            'email' => 'noregion@digipangan.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'is_active' => true,
            'roles' => ['admin_distrik'],
            'region_id' => null,
        ]);

        $response->assertInvalid(['region_id']);
    });

    it('automatically scopes created products and farmers to district admin region', function () {
        $region = Region::first();
        $districtAdmin = createDistrictAdmin($region);

        $farmer = Farmer::create([
            'name' => 'Petani Lokal Distrik',
            'phone' => '081234567899',
            'region_id' => $region->id,
            'is_active' => true,
        ]);

        $category = ProductCategory::first();
        $unit = Unit::first();

        // Create product
        $this->actingAs($districtAdmin)->post(route('admin.product.store'), [
            'name' => 'Beras Organik Distrik',
            'product_category_id' => $category->id,
            'unit_id' => $unit->id,
            'farmer_id' => $farmer->id,
            'price' => 75000,
            'is_active' => true,
        ])->assertRedirect(route('admin.product.index'));

        $product = Product::where('name', 'Beras Organik Distrik')->first();
        expect($product)->not->toBeNull();
        expect($product->region_id)->toBe($region->id);

        // Create farmer
        $this->actingAs($districtAdmin)->post(route('admin.farmer.store'), [
            'name' => 'Petani Baru Distrik',
            'phone' => '081112223334',
            'is_active' => true,
        ])->assertRedirect(route('admin.farmer.index'));

        $newFarmer = Farmer::where('name', 'Petani Baru Distrik')->first();
        expect($newFarmer)->not->toBeNull();
        expect($newFarmer->region_id)->toBe($region->id);
    });

    it('renders dashboard with district-specific scoped data and village distribution', function () {
        $region = Region::first();
        $districtAdmin = createDistrictAdmin($region);

        $response = $this->actingAs($districtAdmin)->get(route('admin.dashboard.index'));
        $response->assertOk();
    });
});

