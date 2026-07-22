<?php

namespace Modules\Page\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Page\Models\Faq;
use Modules\Page\Models\Partner;
use Modules\Page\Models\SiteSetting;

class PageDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPartners();
        $this->seedSettings();
        $this->seedFaqs();
    }

    private function seedPartners(): void
    {
        $partners = [
            [
                'name' => 'Universitas Gadjah Mada',
                'website_url' => 'https://ugm.ac.id',
                'description' => 'Pendamping program pengembangan kawasan transmigrasi Merauke.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Kementerian Transmigrasi RI',
                'website_url' => null,
                'description' => 'Mitra pemerintah dalam program transmigrasi kawasan Merauke.',
                'sort_order' => 2,
            ],
        ];

        foreach ($partners as $partner) {
            Partner::firstOrCreate(['name' => $partner['name']], $partner);
        }
    }

    /**
     * Isi latar belakang dan tujuan diringkas dari narasi program pada PRD
     * (dokumen program UGM × Kementerian Transmigrasi), bukan karangan bebas.
     * Kontak admin sengaja dibiarkan kosong: itu data milik pemilik program
     * yang harus diisi sendiri sebelum rilis, bukan diseed dengan nilai palsu.
     * Nilai default hanya ditulis saat kolom masih kosong, agar suntingan admin
     * tidak tertimpa saat seeder dijalankan ulang.
     */
    private function seedSettings(): void
    {
        $settings = [
            [
                'key' => 'about_background',
                'type' => 'richtext',
                'default' => 'DigiPangan Merauke adalah platform digital yang menjadi etalase komoditas lokal kawasan transmigrasi di Kabupaten Merauke, meliputi Distrik Muting, Ulilin, dan Elikobel. Kawasan ini memiliki potensi komoditas unggulan seperti padi, jagung, rambutan, ubi kayu, kopi, pinang, dan karet, serta hortikultura seperti cabai, tomat, kubis, kangkung, dan kacang panjang. Potensi tersebut menghadapi tantangan berupa produktivitas yang belum optimal, akses pasar yang terbatas, dan literasi digital yang masih rendah. Platform ini dikembangkan sebagai komponen dukungan teknologi dalam program pendampingan Universitas Gadjah Mada bersama Kementerian Transmigrasi.',
            ],
            [
                'key' => 'about_purpose',
                'type' => 'richtext',
                'default' => 'DigiPangan hadir untuk menjadi etalase digital produk komoditas lokal di setiap wilayah transmigrasi, mempromosikan kawasan Muting, Ulilin, dan Elikobel, serta mendukung digitalisasi pemasaran dan peningkatan kapasitas sumber daya manusia petani. Pengunjung dapat melihat profil produk, petani, dan wilayah, lalu terhubung langsung dengan penjual melalui tombol Hubungi Penjual.',
            ],
            ['key' => 'admin_contact_name', 'type' => 'text', 'default' => null],
            ['key' => 'admin_contact_phone', 'type' => 'phone', 'default' => null],
            ['key' => 'admin_contact_email', 'type' => 'email', 'default' => null],
        ];

        foreach ($settings as $data) {
            $setting = SiteSetting::firstOrNew(['key' => $data['key']]);
            $setting->type = $data['type'];

            if (blank($setting->value) && filled($data['default'] ?? null)) {
                $setting->value = $data['default'];
            }

            $setting->save();
        }
    }

    private function seedFaqs(): void
    {
        foreach ($this->faqs() as $index => $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                ['answer' => $faq['answer'], 'sort_order' => $index + 1, 'is_active' => true],
            );
        }
    }

    private function faqs(): array
    {
        return [
            [
                'question' => 'Bagaimana cara membeli produk di DigiPangan?',
                'answer' => 'DigiPangan adalah etalase digital. Setelah menemukan produk yang diminati, tekan tombol Hubungi Penjual untuk terhubung langsung dengan petani atau kelompok tani. Transaksi dan pengiriman disepakati langsung antara pembeli dan penjual.',
            ],
            [
                'question' => 'Apakah harga yang tercantum sudah pasti?',
                'answer' => 'Harga yang ditampilkan adalah harga acuan dari petani. Detail akhir, termasuk jumlah dan pengiriman, sebaiknya dikonfirmasi langsung saat menghubungi penjual.',
            ],
            [
                'question' => 'Wilayah mana saja yang tersedia di DigiPangan?',
                'answer' => 'Saat ini DigiPangan menampilkan komoditas dari tiga distrik transmigrasi di Kabupaten Merauke, yaitu Muting, Ulilin, dan Elikobel.',
            ],
            [
                'question' => 'Siapa yang dapat menjual produk di platform ini?',
                'answer' => 'Produk berasal dari petani dan kelompok tani di kawasan transmigrasi yang datanya dikelola oleh admin. Petani belum mendaftar sendiri; pendataan dilakukan melalui pendampingan program.',
            ],
            [
                'question' => 'Bagaimana jika produk yang saya cari sedang tidak tersedia?',
                'answer' => 'Ketersediaan produk mengikuti musim panen dan stok petani. Anda dapat memeriksa kembali secara berkala atau menghubungi penjual untuk menanyakan ketersediaan.',
            ],
        ];
    }
}
