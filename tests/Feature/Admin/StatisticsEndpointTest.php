<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Modules\Farmer\Models\Farmer;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductInteraction;
use Modules\Product\Models\Unit;
use Modules\Region\Models\Region;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_stats(string $role = 'super_admin'): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

function stats_product(Farmer $farmer, bool $active = true): Product
{
    return Product::create([
        'product_category_id' => ProductCategory::create(['name' => 'Kat '.uniqid()])->id,
        'unit_id' => Unit::create(['name' => 'Sat '.uniqid(), 'symbol' => substr(uniqid(), -8)])->id,
        'farmer_id' => $farmer->id,
        'name' => 'Produk '.uniqid(),
        'price' => 10000,
        'is_active' => $active,
    ]);
}

function stats_interaction(Product $product, string $type): void
{
    ProductInteraction::create([
        'product_id' => $product->id,
        'region_id' => $product->region_id,
        'type' => $type,
        'occurred_at' => now(),
    ]);
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('Dashboard statistics', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.statistics.summary'))->assertStatus(401);
    });

    it('menolak pengguna tanpa izin lihat statistik dengan 403', function () {
        $this->actingAs(User::factory()->create(['is_active' => true]))
            ->getJson(route('api.statistics.summary'))
            ->assertStatus(403);
    });

    it('mengembalikan ringkasan metrik M-01..M-07', function () {
        $regionA = Region::create(['name' => 'Ulilin']);
        $regionB = Region::create(['name' => 'Muting']);

        $farmerA = Farmer::create(['region_id' => $regionA->id, 'name' => 'Petani A', 'phone' => '+6281000000001']);
        Farmer::create(['region_id' => $regionB->id, 'name' => 'Petani B', 'phone' => '+6281000000002']);
        Farmer::create(['region_id' => $regionA->id, 'name' => 'Petani C', 'phone' => '+6281000000003', 'is_active' => false]);

        $contacted = stats_product($farmerA);
        stats_product($farmerA);                 // never contacted, active
        stats_product($farmerA, active: false);  // inactive, excluded

        stats_interaction($contacted, 'view');
        stats_interaction($contacted, 'view');
        stats_interaction($contacted, 'contact');

        $admin = actor_stats();
        DB::table('audits')->insert([
            'user_type' => User::class,
            'user_id' => $admin->id,
            'event' => 'updated',
            'auditable_type' => Product::class,
            'auditable_id' => $contacted->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($admin)
            ->getJson(route('api.statistics.summary'))
            ->assertOk()
            ->assertJsonPath('data.products_active', 2)
            ->assertJsonPath('data.farmers_complete', 2)
            ->assertJsonPath('data.views_total', 2)
            ->assertJsonPath('data.contacts_total', 1)
            ->assertJsonPath('data.contact_view_ratio', 0.5)
            ->assertJsonPath('data.products_contacted', 1)
            ->assertJsonPath('data.products_never_contacted', 1)
            ->assertJsonCount(12, 'data.contacts_monthly')
            ->assertJsonCount(12, 'data.audits_monthly')
            ->assertJsonFragment(['region' => 'Ulilin', 'count' => 1])
            ->assertJsonFragment(['region' => 'Muting', 'count' => 0]);

        $contactsMonthly = $response->json('data.contacts_monthly');
        $auditsMonthly = $response->json('data.audits_monthly');

        expect(end($contactsMonthly)['count'])->toBe(1)
            ->and(end($auditsMonthly)['count'])->toBe(1);
    });

    it('mendaftar produk aktif yang belum pernah dikontak', function () {
        $region = Region::create(['name' => 'Ulilin']);
        $farmer = Farmer::create(['region_id' => $region->id, 'name' => 'Petani', 'phone' => '+6281000000001']);

        $contacted = stats_product($farmer);
        $never = stats_product($farmer);
        stats_product($farmer, active: false);
        stats_interaction($contacted, 'contact');

        $this->actingAs(actor_stats())
            ->getJson(route('api.statistics.uncontacted'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $never->id);
    });
});
