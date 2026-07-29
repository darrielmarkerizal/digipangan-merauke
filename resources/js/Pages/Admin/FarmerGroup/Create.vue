<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ArrowLeft, Save, Users, MapPin } from '@lucide/vue'
import { Button, Field, Input, Select, Icon } from '@/Components/ui'
import { toast } from 'vue-sonner'

const props = defineProps<{
  regions: any[]
}>()

const form = useForm({
  name: '',
  region_id: '' as string | number,
})

const handleSubmit = () => {
  form.post('/admin/kelompok-tani', {
    onSuccess: () => {
      toast.success('Kelompok tani baru berhasil ditambahkan.')
    },
    onError: () => {
      toast.error('Gagal menambahkan kelompok tani. Periksa kembali isian Anda.')
    },
  })
}
</script>

<template>
  <AdminLayout
    title="Tambah Kelompok Tani"
    subtitle="Tambahkan kelompok tani baru beserta distrik wilayahnya."
  >
    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <Link
          href="/admin/kelompok-tani"
          class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
        >
          <Icon :icon="ArrowLeft" :size="16" />
          <span>Kembali ke Daftar Kelompok Tani</span>
        </Link>
      </div>

      <form @submit.prevent="handleSubmit" class="max-w-2xl">
        <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-6">
          <div class="flex items-center gap-2 border-b border-border/60 pb-3">
            <Icon :icon="Users" :size="18" class="text-brand" />
            <h3 class="text-sm font-bold text-fg">Informasi Kelompok Tani</h3>
          </div>

          <div class="space-y-5">
            <Field label="Distrik / Kawasan" :error="form.errors.region_id" required>
              <Select v-model="form.region_id" required>
                <option value="">Pilih Distrik...</option>
                <option v-for="r in regions" :key="r.id" :value="r.id">
                  {{ r.name }}
                </option>
              </Select>
            </Field>

            <Field label="Nama Kelompok Tani" :error="form.errors.name" required>
              <Input
                v-model="form.name"
                placeholder="Contoh: Kelompok Tani Elikobel"
                required
              />
            </Field>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-border/60">
            <Link href="/admin/kelompok-tani">
              <Button type="button" variant="secondary" size="sm">
                Batal
              </Button>
            </Link>
            <Button
              type="submit"
              size="sm"
              :loading="form.processing"
              class="gap-1.5 font-semibold"
            >
              <Icon :icon="Save" :size="16" />
              <span>Simpan Kelompok Tani</span>
            </Button>
          </div>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
