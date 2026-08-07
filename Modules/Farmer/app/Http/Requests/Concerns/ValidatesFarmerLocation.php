<?php

namespace Modules\Farmer\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Modules\Farmer\Models\FarmerGroup;
use Modules\Region\Models\Village;

trait ValidatesFarmerLocation
{
    /**
     * Ensure the chosen village and farmer group both belong to the chosen
     * region. The Vue forms already cascade these client-side; this guards
     * against a crafted POST attaching a cross-region village or group.
     */
    protected function validateFarmerLocationConsistency(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $regionId = (int) $this->input('region_id');

            if ($this->filled('farmer_group_id')) {
                $group = FarmerGroup::find($this->input('farmer_group_id'));

                if ($group && (int) $group->region_id !== $regionId) {
                    $validator->errors()->add('farmer_group_id', 'Kelompok tani yang dipilih tidak berada di wilayah yang sama.');
                }
            }

            if ($this->filled('village_id')) {
                $village = Village::find($this->input('village_id'));

                if ($village && (int) $village->region_id !== $regionId) {
                    $validator->errors()->add('village_id', 'Desa / kampung yang dipilih tidak berada di wilayah yang sama.');
                }
            }
        });
    }
}
