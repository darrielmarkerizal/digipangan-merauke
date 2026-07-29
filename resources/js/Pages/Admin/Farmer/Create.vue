<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import FarmerForm from './FarmerForm.vue'

defineProps<{
  regions?: any[]
  villages?: any[]
  farmerGroups?: any[]
  commodities?: any[]
}>()
</script>

<template>
  <AdminLayout
    title="Tambah Petani / Mitra Baru"
    subtitle="Daftarkan profil petani lokal, kontak WhatsApp, desa asal, serta tetapkan komoditas yang dibudidayakan."
  >
    <div class="w-full pb-10">
      <FarmerForm
        :regions="regions"
        :villages="villages"
        :farmer-groups="farmerGroups"
        :commodities="commodities"
      />
    </div>
  </AdminLayout>
</template>
