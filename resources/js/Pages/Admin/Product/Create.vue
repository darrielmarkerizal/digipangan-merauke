<script setup lang="ts">
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ProductForm from "@/Components/admin/Product/ProductForm.vue"

defineProps<{
  categories?: any[]
  units?: any[]
  farmers?: any[]
  regions?: any[]
  default_region_id?: number | string
}>()
</script>

<template>
  <AdminLayout
    title="Tambah Produk Pangan Baru"
    subtitle="Lengkapi formulir di bawah ini untuk menambahkan komoditas hasil panen baru ke etalase publik DigiPangan Merauke."
  >
    <div class="w-full pb-10">
      <ProductForm
        :categories="categories"
        :units="units"
        :farmers="farmers"
        :regions="regions"
        :default-region-id="default_region_id"
      />
    </div>
  </AdminLayout>
</template>
