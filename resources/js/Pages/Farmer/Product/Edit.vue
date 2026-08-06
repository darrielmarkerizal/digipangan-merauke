<script setup lang="ts">
import FarmerLayout from '@/Layouts/FarmerLayout.vue'
import FarmerProductForm from '@/Components/farmer-dashboard/FarmerProductForm.vue'

defineProps<{
  product?: any
  categories?: any[]
  units?: any[]
}>()
</script>

<template>
  <FarmerLayout
    title="Edit Produk"
    subtitle="Perbarui informasi, harga, dan foto produk Anda."
  >
    <div class="w-full pb-10">
      <FarmerProductForm
        :initial-data="product"
        :is-edit="true"
        :categories="categories"
        :units="units"
      />
    </div>
  </FarmerLayout>
</template>
