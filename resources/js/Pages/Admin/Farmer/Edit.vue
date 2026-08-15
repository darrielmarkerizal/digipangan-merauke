<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FarmerForm from "@/Components/admin/Farmer/FarmerForm.vue"

const props = defineProps<{
  farmer?: any
  regions?: any[]
  villages?: any[]
  farmerGroups?: any[]
  commodities?: any[]
  default_region_id?: number | string
}>()
</script>

<template>
  <AdminLayout
    :title="`Edit Profil: ${props.farmer?.name || 'Petani'}`"
    subtitle="Perbarui identitas, kontak WhatsApp, distrik, desa asal, serta komoditas budidaya petani."
  >
    <div class="w-full pb-10">
      <FarmerForm
        :initial-data="props.farmer"
        :is-edit="true"
        :regions="regions"
        :villages="villages"
        :farmer-groups="farmerGroups"
        :commodities="commodities"
        :default-region-id="default_region_id"
      />
    </div>
  </AdminLayout>
</template>
