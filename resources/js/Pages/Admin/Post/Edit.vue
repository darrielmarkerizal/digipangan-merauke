<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import PostForm from "@/Components/admin/Post/PostForm.vue"

const props = defineProps<{
  post: any
  categories: any[]
}>()

const form = useForm({
  post_category_id: props.post.post_category_id || '',
  title: props.post.title || '',
  body: props.post.body || '',
  status: props.post.status || 'draft',
  cover: props.post.cover_url || '',
})

const handleSubmit = () => {
  form.put(`/admin/berita/${props.post.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <AdminLayout
    :title="`Edit Berita: ${post.title}`"
    subtitle="Perbarui konten, ubah status publikasi, atau ganti gambar sampul."
  >
    <PostForm
      :form="form"
      :categories="categories"
      :isEdit="true"
      @submit="handleSubmit"
    />
  </AdminLayout>
</template>
