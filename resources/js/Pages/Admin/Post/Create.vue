<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PostForm from './PostForm.vue'

const props = defineProps<{
  categories: any[]
}>()

const form = useForm({
  post_category_id: '',
  title: '',
  body: '',
  status: 'draft',
  cover: '',
})

const handleSubmit = () => {
  form.post('/admin/berita', {
    preserveScroll: true,
  })
}
</script>

<template>
  <AdminLayout
    title="Tulis Berita Baru"
    subtitle="Buat publikasi konten, artikel, atau informasi pelatihan."
  >
    <PostForm
      :form="form"
      :categories="categories"
      @submit="handleSubmit"
    />
  </AdminLayout>
</template>
