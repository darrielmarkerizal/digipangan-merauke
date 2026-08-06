<script setup lang="ts">
import FarmerLayout from '@/Layouts/FarmerLayout.vue'
import FarmerProductForm from '@/Components/farmer-dashboard/FarmerProductForm.vue'

defineProps<{
  categories?: any[]
  units?: any[]
}>()
</script>

<template>
  <FarmerLayout
    title="Tambah Produk Baru"
    subtitle="Lengkapi informasi produk hasil panen yang ingin Anda pasarkan."
  >
    <div class="w-full pb-10">
      <FarmerProductForm :categories="categories" :units="units" />
    </div>
  </FarmerLayout>
</template>
