<?php

use App\Models\User;
use Modules\Page\Models\Faq;
use Modules\User\Database\Seeders\UserDatabaseSeeder;

function actor_faq(string $role = 'super_admin'): User
{
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole($role);

    return $user;
}

beforeEach(function () {
    app(UserDatabaseSeeder::class)->run();
    $this->withHeader('Origin', config('app.url'));
});

describe('FAQ CRUD', function () {
    it('menolak tamu dengan 401', function () {
        $this->getJson(route('api.faq.index'))->assertStatus(401);
    });

    it('mengizinkan admin mengakses daftar', function () {
        $this->actingAs(actor_faq('admin'))->getJson(route('api.faq.index'))->assertOk();
    });

    it('menolak pengguna tanpa izin kelola tentang dengan 403', function () {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)->getJson(route('api.faq.index'))->assertStatus(403);
    });

    it('membuat FAQ baru', function () {
        $this->actingAs(actor_faq())
            ->postJson(route('api.faq.store'), [
                'question' => 'Bagaimana cara menghubungi penjual?',
                'answer' => 'Gunakan tombol Hubungi Penjual.',
            ])
            ->assertCreated()
            ->assertJsonPath('data.question', 'Bagaimana cara menghubungi penjual?');

        $this->assertDatabaseHas('faqs', ['question' => 'Bagaimana cara menghubungi penjual?']);
    });

    it('menolak pertanyaan kosong dengan 422', function () {
        $this->actingAs(actor_faq())
            ->postJson(route('api.faq.store'), ['answer' => 'Tanpa pertanyaan.'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('question');
    });

    it('memperbarui FAQ', function () {
        $faq = Faq::create(['question' => 'Lama?', 'answer' => 'Jawaban lama.']);

        $this->actingAs(actor_faq())
            ->putJson(route('api.faq.update', $faq->id), ['question' => 'Baru?', 'answer' => 'Jawaban baru.'])
            ->assertOk()
            ->assertJsonPath('data.question', 'Baru?');
    });

    it('mengurutkan daftar berdasarkan sort_order', function () {
        Faq::create(['question' => 'Kedua', 'answer' => 'B', 'sort_order' => 2]);
        Faq::create(['question' => 'Pertama', 'answer' => 'A', 'sort_order' => 1]);

        $this->actingAs(actor_faq())
            ->getJson(route('api.faq.index'))
            ->assertOk()
            ->assertJsonPath('data.0.question', 'Pertama')
            ->assertJsonPath('data.1.question', 'Kedua');
    });

    it('menghapus FAQ (soft delete)', function () {
        $faq = Faq::create(['question' => 'Hapus?', 'answer' => 'Ya.']);

        $this->actingAs(actor_faq())
            ->deleteJson(route('api.faq.destroy', $faq->id))
            ->assertOk();

        $this->assertSoftDeleted('faqs', ['id' => $faq->id]);
    });
});
