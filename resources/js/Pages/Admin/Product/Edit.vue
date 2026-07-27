<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ProductForm, { type ProductFormData } from './ProductForm.vue'

const props = defineProps<{
  id?: number | string
  product?: ProductFormData
  categories?: any[]
  units?: any[]
  farmers?: any[]
  regions?: any[]
}>()
</script>

<template>
  <AdminLayout
    :title="`Edit Produk: ${props.product?.name || 'Produk'}`"
    subtitle="Perbarui data harga, stok, ketersediaan, atau deskripsi produk komoditas ini."
  >
    <div class="w-full pb-10">
      <ProductForm
        :initial-data="props.product"
        :is-edit="true"
        :categories="categories"
        :units="units"
        :farmers="farmers"
        :regions="regions"
      />
    </div>
  </AdminLayout>
</template>
