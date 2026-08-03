<?php

namespace Modules\Region\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Region\Models\Region;
use Modules\Region\Models\Village;

class RegionDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->regions() as $data) {
            $villages = $data['villages'];
            unset($data['villages']);

            $region = Region::firstOrNew(['name' => $data['name']]);
            $region->fill($data)->save();

            foreach ($villages as $name) {
                Village::firstOrCreate([
                    'region_id' => $region->id,
                    'name' => $name,
                ]);
            }
        }
    }

    /**
     * Data luas wilayah dan populasi bersumber dari data resmi pemerintah setempat
     * dan dokumen BPS Kabupaten Merauke.
     */
    private function regions(): array
    {
        return [
            [
                'name' => 'Muting',
                'area_km2' => 3501.67,
                'population' => 5705,
                'description' => 'Distrik Muting di Kabupaten Merauke, Provinsi Papua Selatan, memiliki luas wilayah sekitar 3.501 - 3.868 km² yang terbagi menjadi 12 kampung dengan jumlah populasi penduduk sekitar 5.705 - 6.006 jiwa (didominasi kelompok umur usia muda).',
                'agricultural_potential' => 'Tanaman pangan seperti padi dan jagung menjadi tumpuan warga, ditopang hortikultura dan hasil perkebunan yang dikembangkan kelompok tani setempat.',
                'villages' => [
                    'Muting',
                    'Boha',
                    'Wan (Waan)',
                    'Kolam',
                    'Selauw',
                    'Pachas',
                    'Seed Agung',
                    'Enggol Jaya',
                    'Manway Bop',
                    'Efkab Makmur (Apkap Makmur)',
                    'Sigabel Jaya',
                    'Andaito',
                ],
            ],
            [
                'name' => 'Ulilin',
                'area_km2' => 3633.08,
                'population' => 10791,
                'description' => 'Distrik Ulilin memiliki luas sekitar 3.633,08 km² dengan 11 kampung dan sekitar 10.791 jiwa penduduk. Distrik ini menjadi salah satu fokus pengembangan komoditas pangan pada kawasan transmigrasi Merauke.',
                'agricultural_potential' => 'Komoditas unggulan meliputi padi, jagung, rambutan, ubi kayu, kopi, pinang, dan karet, dilengkapi hortikultura seperti cabai, tomat, kubis, kangkung, dan kacang panjang.',
                'villages' => [
                    'Baidub',
                    'Belbeland',
                    'Kafyamke',
                    'Kandrakai',
                    'Kindiki',
                    'Kireli (Kir-ely)',
                    'Kumaaf',
                    'Mandekman',
                    'Nggayu',
                    'Rawahayu',
                    'Selil',
                ],
            ],
            [
                'name' => 'Elikobel',
                'area_km2' => 2366.9,
                'population' => 4569,
                'description' => 'Distrik Elikobel (Eligobel) di Kabupaten Merauke, Provinsi Papua Selatan, memiliki luas wilayah sekitar 2.366,9 km² (mencakup sekitar 3,24% dari total luas Kabupaten Merauke) dan jumlah populasi penduduk tercatat sebanyak 4.569 jiwa (2.414 laki-laki dan 2.155 perempuan).',
                'agricultural_potential' => 'Cabai, tomat, kopi, dan ubi menjadi komoditas yang banyak dihasilkan petani setempat, di samping tanaman pangan lainnya.',
                'villages' => [
                    'Bupul (Bupul Indah)',
                    'Kweel',
                    'Tanaas (Tanas)',
                    'Sipias',
                    'Metaat Makmur',
                    'Enggal Jaya',
                    'Gerisar',
                    'Totob (Toftof)',
                    "Bumun (B'men)",
                    'Bunggei (Bunggai)',
                    "Bower (Bow'r)",
                    'Kandrakae (Alongglong)',
                ],
            ],
        ];
    }
}
