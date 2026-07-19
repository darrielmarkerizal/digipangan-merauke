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

    private function regions(): array
    {
        return [
            [
                'name' => 'Muting',
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
