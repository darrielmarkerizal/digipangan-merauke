<script setup lang="ts">
import FarmerLayout from '@/Layouts/FarmerLayout.vue'
import FarmerProfileForm from '@/Components/farmer-dashboard/FarmerProfileForm.vue'

defineProps<{
  farmer?: any
  regions?: any[]
  villages?: any[]
  farmerGroups?: any[]
  commodities?: any[]
}>()
</script>

<template>
  <FarmerLayout
    title="Profil Saya"
    subtitle="Perbarui identitas, kontak, wilayah, dan kelompok tani Anda."
  >
    <div class="w-full pb-10">
      <FarmerProfileForm
        :initial-data="farmer"
        :regions="regions"
        :villages="villages"
        :farmer-groups="farmerGroups"
        :commodities="commodities"
      />
    </div>
  </FarmerLayout>
</template>
