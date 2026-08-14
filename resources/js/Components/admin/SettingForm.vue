<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { toast } from 'vue-sonner'
import { Save, Info, Phone, Building2 } from '@lucide/vue'
import { Field, Input, PhoneInput, Button, Icon, Spinner } from '@/Components/ui'
import { QuillEditor } from "@vueup/vue-quill"
import "@vueup/vue-quill/dist/vue-quill.snow.css"

const form = ref({
  about_background: '',
  about_purpose: '',
  admin_contact_name: '',
  admin_contact_phone: '',
  admin_contact_email: '',
})

const isLoading = ref(true)
const isSaving = ref(false)
const errors = ref<Record<string, string[]>>({})

const fetchSettings = async () => {
  isLoading.value = true
  try {
    const response = await axios.get('/api/v1/site-settings')
    const data = response.data.data
    data.forEach((item: any) => {
      if (item.key in form.value) {
        (form.value as any)[item.key] = item.value || ''
      }
    })
  } catch (error) {
    toast.error('Gagal memuat pengaturan situs.')
  } finally {
    isLoading.value = false
  }
}

const handleSave = async () => {
  isSaving.value = true
  errors.value = {}
  
  try {
    await axios.put('/api/v1/site-settings', form.value)
    toast.success('Pengaturan situs berhasil diperbarui.')
  } catch (error: any) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors || {}
      toast.error('Gagal menyimpan. Periksa kembali form isian.')
    } else {
      toast.error('Terjadi kesalahan saat menyimpan pengaturan.')
    }
  } finally {
    isSaving.value = false
  }
}

onMounted(() => {
  fetchSettings()
})
</script>

<template>
  <div>
    <div v-if="isLoading" class="flex justify-center py-12">
      <Spinner class="text-brand size-8" />
    </div>

    <form v-else @submit.prevent="handleSave" class="space-y-6 pb-10">
      <div class="flex items-center justify-end">
        <Button
          type="submit"
          size="sm"
          :loading="isSaving"
          class="gap-1.5 font-semibold"
        >
          <Icon :icon="Save" :size="16" />
          <span>Simpan Perubahan</span>
        </Button>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5 h-full">
          <div class="flex items-center gap-2 border-b border-border/60 pb-3">
            <Icon :icon="Building2" :size="18" class="text-brand" />
            <h3 class="text-sm font-bold text-fg">Narasi Halaman Tentang</h3>
          </div>
          
          <p class="text-xs text-fg-muted pb-2">
            Teks yang akan ditampilkan pada halaman Tentang Kami di sisi publik.
          </p>

          <div class="space-y-4">
            <Field label="Latar Belakang Program" :error="errors.about_background?.[0]">
              <div class="border rounded-md border-border/80 overflow-hidden" :class="{'border-danger': !!errors.about_background}">
                <QuillEditor
                  theme="snow"
                  v-model:content="form.about_background"
                  contentType="html"
                  placeholder="Tuliskan latar belakang program DigiPangan..."
                  style="min-height: 200px;"
                />
              </div>
            </Field>

            <Field label="Tujuan Platform" :error="errors.about_purpose?.[0]">
              <div class="border rounded-md border-border/80 overflow-hidden" :class="{'border-danger': !!errors.about_purpose}">
                <QuillEditor
                  theme="snow"
                  v-model:content="form.about_purpose"
                  contentType="html"
                  placeholder="Tuliskan tujuan dari platform ini..."
                  style="min-height: 200px;"
                />
              </div>
            </Field>
          </div>
        </div>

        <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-5 h-full">
          <div class="flex items-center gap-2 border-b border-border/60 pb-3">
            <Icon :icon="Phone" :size="18" class="text-brand" />
            <h3 class="text-sm font-bold text-fg">Informasi Kontak Publik</h3>
          </div>
          
          <p class="text-xs text-fg-muted pb-2">
            Informasi yang ditampilkan agar pengguna dapat menghubungi pengelola.
          </p>

          <div class="space-y-4">
            <Field label="Nama Kontak (Admin)" :error="errors.admin_contact_name?.[0]">
              <Input
                v-model="form.admin_contact_name"
                placeholder="Misal: Admin DigiPangan"
              />
            </Field>

            <Field label="Nomor WhatsApp" :error="errors.admin_contact_phone?.[0]">
              <PhoneInput
                v-model="form.admin_contact_phone"
                placeholder="81234567890"
              />
            </Field>

            <Field label="Alamat Email" :error="errors.admin_contact_email?.[0]">
              <Input
                v-model="form.admin_contact_email"
                type="email"
                placeholder="Misal: admin@digipangan.id"
              />
            </Field>
          </div>
          
          <div class="mt-6 p-4 rounded-xl bg-blue-50/50 border border-blue-100 flex gap-3 text-blue-700">
            <Icon :icon="Info" :size="20" class="shrink-0 text-blue-500" />
            <p class="text-xs leading-relaxed">
              <strong>Penting:</strong> Data kontak di atas sangat krusial karena merupakan ujung tombak komunikasi antara pengunjung publik dengan pengelola program DigiPangan Merauke.
            </p>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<style>
.ql-container {
    font-family: inherit !important;
    font-size: 14px !important;
}
.ql-editor {
    min-height: 200px;
}
</style>
