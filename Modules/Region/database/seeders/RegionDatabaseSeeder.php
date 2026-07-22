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
     * Luas wilayah bersumber dari BPS Kabupaten Merauke (relatif stabil karena
     * merujuk batas administratif). Populasi hanya diisi untuk Ulilin karena
     * itulah satu-satunya angka yang terverifikasi konsisten dari dokumen
     * program; angka penduduk Muting/Elikobel dibiarkan null agar tidak
     * mencampur data lintas tahun tanpa konteks tahun pada tampilan.
     */
    private function regions(): array
    {
        return [
            [
                'name' => 'Muting',
                'area_km2' => 3706.32,
                'description' => 'Distrik Muting merupakan salah satu kawasan transmigrasi di Kabupaten Merauke, Provinsi Papua Selatan, yang terdiri atas 11 kampung. Wilayah ini menjadi bagian dari program pengembangan kawasan transmigrasi yang didampingi Universitas Gadjah Mada bersama Kementerian Transmigrasi.',
                'agricultural_potential' => 'Tanaman pangan seperti padi dan jagung menjadi tumpuan warga, ditopang hortikultura dan hasil perkebunan yang dikembangkan kelompok tani setempat.',
                'villages' => [
                    'Kampung Muting',
                    'Waan',
                    'Kolam',
                    'Selauw',
                    'Pahas',
                    'Afnaf Makmur',
                    'Boha',
                    'Andaito',
                    'Sigabel Jaya',
                    'Elngol Jaya',
                    'Seed Agung Prasasti',
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
                    'Kireli',
                    'Kumaaf',
                    'Mandekman',
                    'Nggayu',
                    'Rawahayu',
                    'Selil Merauke',
                ],
            ],
            [
                'name' => 'Elikobel',
                'area_km2' => 2366.9,
                'description' => 'Distrik Elikobel adalah kawasan transmigrasi di Kabupaten Merauke yang terdiri atas 12 kampung. Wilayah ini dikenal dengan aktivitas kelompok tani yang menggarap komoditas hortikultura dan perkebunan.',
                'agricultural_potential' => 'Cabai, tomat, kopi, dan ubi menjadi komoditas yang banyak dihasilkan petani setempat, di samping tanaman pangan lainnya.',
                'villages' => [
                    'Bouwer',
                    'Bumun',
                    'Bunggay',
                    'Bupul',
                    'Bupul Indah',
                    'Enggal Jaya',
                    'Gerisar',
                    'Kweel',
                    'Metaat Makmur',
                    'Sipias',
                    'Tanas',
                    'Tof-Tof Merauke',
                ],
            ],
        ];
    }
}
