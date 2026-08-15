<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'
import axios from 'axios'
import {
  ArrowLeft,
  Save,
  Upload,
  Package,
  MapPin,
  DollarSign,
  Image as ImageIcon,
  Sparkles,
  Star,
  Trash2,
  Plus,
} from '@lucide/vue'
import { toast } from 'vue-sonner'
import {
  Button,
  Field,
  Input,
  Select,
  Textarea,
  Icon,
} from '@/Components/ui'
import { formatRupiah } from '@/lib/format'

export interface ProductFormData {
  id?: number | string
  name?: string
  product_category_id?: number | string
  category?: { id: number | string; name: string }
  farmer_id?: number | string
  farmer?: { id: number | string; name: string }
  region_id?: number | string
  region?: { id: number | string; name: string }
  unit_id?: number | string
  unit?: { id: number | string; name: string }
  price?: number | string
  weight_value?: number | string
  description?: string
  stock_available?: boolean
  is_featured?: boolean
  is_region_featured?: boolean
  is_active?: boolean
  image_url?: string
  photos?: Array<{ id: number | string; thumb: string; original: string }>
}

export interface GalleryImage {
  id: string
  url: string
  file?: File
  isExisting?: boolean
  mediaId?: number | string
}

const props = defineProps<{
  initialData?: ProductFormData
  isEdit?: boolean
  defaultRegionId?: number | string
  categories?: Array<{ id: number | string; name: string }>
  units?: Array<{ id: number | string; name: string; symbol?: string }>
  farmers?: Array<{ id: number | string; name: string }>
  regions?: Array<{ id: number | string; name: string }>
}>()

const form = useForm({
  name: props.initialData?.name || '',
  product_category_id: props.initialData?.product_category_id || props.initialData?.category?.id || (props.categories?.[0]?.id ?? ''),
  farmer_id: props.initialData?.farmer_id || props.initialData?.farmer?.id || (props.farmers?.[0]?.id ?? ''),
  region_id: props.initialData?.region_id || props.initialData?.region?.id || props.defaultRegionId || (props.regions?.[0]?.id ?? ''),
  unit_id: props.initialData?.unit_id || props.initialData?.unit?.id || (props.units?.[0]?.id ?? ''),
  price: props.initialData?.price || '',
  weight_value: props.initialData?.weight_value || '',
  description: props.initialData?.description || '',
  stock_available: props.initialData?.stock_available ?? true,
  is_featured: props.initialData?.is_featured ?? false,
  is_region_featured: props.initialData?.is_region_featured ?? false,
  is_active: props.initialData?.is_active ?? true,
})

const displayPrice = computed({
  get: () => formatRupiah(form.price, false),
  set: (val: string) => {
    const digits = val.replace(/\D/g, '')
    form.price = digits ? Number(digits) : ''
  },
})

const galleryImages = ref<GalleryImage[]>(
  props.initialData?.photos && props.initialData.photos.length > 0
    ? props.initialData.photos.map((p) => ({
        id: String(p.id),
        url: p.original || p.thumb,
        isExisting: true,
        mediaId: p.id,
      }))
    : props.initialData?.image_url
    ? [{ id: '1', url: props.initialData.image_url, isExisting: false }]
    : []
)

const isSubmitting = ref(false)

const handleMultipleImageUpload = (e: Event) => {
  const target = e.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const filesArray = Array.from(target.files)
    filesArray.forEach((file) => {
      galleryImages.value.push({
        id: Math.random().toString(36).substring(2, 9),
        url: URL.createObjectURL(file),
        file: file,
        isExisting: false,
      })
    })
    toast.success(`${filesArray.length} foto baru ditambahkan ke galeri!`)
  }
}

const setAsCover = (index: number) => {
  if (index === 0) return
  const [targetImage] = galleryImages.value.splice(index, 1)
  galleryImages.value.unshift(targetImage)
  toast.success('Foto terpilih berhasil dijadikan Cover Utama (Gambar 1)!')
}

const removeImage = (index: number) => {
  galleryImages.value.splice(index, 1)
  toast.success('Foto dihapus dari galeri.')
}

type ToggleKey = 'stock_available' | 'is_active' | 'is_featured' | 'is_region_featured'

const toggleFields: { key: ToggleKey; label: string; description: string }[] = [
  { key: 'stock_available', label: 'Stok Tersedia', description: 'Tandai jika produk ready untuk dibeli' },
  { key: 'is_active', label: 'Tampilkan di Publik', description: 'Aktifkan untuk menampilkan di etalase' },
  { key: 'is_featured', label: 'Unggulan Utama', description: 'Tampilkan di slider / section favorit' },
  { key: 'is_region_featured', label: 'Unggulan Kawasan', description: 'Highlight khusus komoditas khas kawasan' },
]

async function uploadFileToTemp(file: File): Promise<string> {
  const formData = new FormData()
  formData.append('file', file)
  const res = await axios.post('/admin/media/upload', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  })
  return res.data.folder
}

const handleSubmit = async () => {
  if (!form.name || !form.price) {
    toast.error('Gagal menyimpan', {
      description: 'Mohon lengkapi Nama Produk dan Harga Produk.',
    })
    return
  }

  isSubmitting.value = true

  try {
    const newFileItems = galleryImages.value.filter((img) => !img.isExisting && img.file)
    const uploadedFolderUuids: string[] = []

    if (newFileItems.length > 0) {
      toast.info(`Mengunggah ${newFileItems.length} foto produk...`)
      for (const item of newFileItems) {
        if (item.file) {
          const folderUuid = await uploadFileToTemp(item.file)
          uploadedFolderUuids.push(folderUuid)
        }
      }
    }

    const retainedIds = galleryImages.value
      .filter((img) => img.isExisting && img.mediaId !== undefined)
      .map((img) => img.mediaId)

    const endpoint = props.isEdit && props.initialData?.id
      ? `/admin/produk/${props.initialData.id}`
      : '/admin/produk'

    form.transform((data) => ({
      ...data,
      photos: uploadedFolderUuids,
      retained_photos: props.isEdit ? retainedIds : undefined,
    }))

    if (props.isEdit && props.initialData?.id) {
      form.submit('put', endpoint, {
        onSuccess: () => {
          toast.success('Berhasil memperbarui produk!')
        },
        onError: (errors) => {
          toast.error('Gagal memperbarui produk', {
            description: (Object.values(errors)[0] as string) || 'Mohon periksa kembali isian formulir.',
          })
        },
        onFinish: () => {
          isSubmitting.value = false
        },
      })
    } else {
      form.post(endpoint, {
        onSuccess: () => {
          toast.success('Produk baru berhasil ditambahkan!')
        },
        onError: (errors) => {
          toast.error('Gagal menambah produk', {
            description: (Object.values(errors)[0] as string) || 'Mohon periksa kembali isian formulir.',
          })
        },
        onFinish: () => {
          isSubmitting.value = false
        },
      })
    }
  } catch (err: any) {
    isSubmitting.value = false
    toast.error('Gagal mengunggah foto', {
      description: err.response?.data?.message || err.message || 'Terjadi kesalahan saat mengunggah foto.',
    })
  }
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <div class="flex items-center justify-between gap-4">
      <Link
        href="/admin/produk"
        class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
      >
        <Icon :icon="ArrowLeft" :size="16" />
        <span>Kembali ke Daftar Produk</span>
      </Link>

      <div class="flex items-center gap-3">
        <Link href="/admin/produk">
          <Button variant="secondary" size="sm" type="button">Batal</Button>
        </Link>
        <Button size="sm" type="submit" :disabled="form.processing || isSubmitting">
          <Icon :icon="Save" :size="15" />
          <span>{{ (form.processing || isSubmitting) ? 'Mengunggah &amp; Menyimpan...' : isEdit ? 'Simpan Perubahan' : 'Simpan Produk' }}</span>
        </Button>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2 space-y-6">
        <div class="rounded-2xl border border-border/80 bg-white p-5 md:p-6 shadow-xs space-y-5">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="Package" :size="18" />
            </span>
            <div>
              <h2 class="text-base font-bold text-fg">Informasi Utama Produk</h2>
              <p class="text-xs text-fg-muted">Nama, kategori, dan deskripsi detail produk komoditas</p>
            </div>
          </div>

          <Field label="Nama Produk" required helper="Gunakan nama yang jelas dan spesifik (contoh: Beras Mamberamo Merauke 5kg)">
            <Input v-model="form.name" placeholder="Masukkan nama produk..." />
          </Field>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Field label="Kategori Produk" required>
              <Select v-model="form.product_category_id">
                <option v-for="cat in (categories || [])" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </Select>
            </Field>

            <Field label="Nilai Berat / Takaran" helper="Contoh: 5.00 untuk 5 Kg">
              <Input v-model="form.weight_value" type="number" step="0.01" placeholder="5.00" />
            </Field>
          </div>

          <Field label="Deskripsi Produk" helper="Jelaskan kualitas panen, rasa, varietas, dan petunjuk penyimpanan">
            <Textarea
              v-model="form.description"
              :rows="4"
              placeholder="Tuliskan deskripsi lengkap produk hasil panen..."
            />
          </Field>
        </div>

        <div class="rounded-2xl border border-border/80 bg-white p-5 md:p-6 shadow-xs space-y-5">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="DollarSign" :size="18" />
            </span>
            <div>
              <h2 class="text-base font-bold text-fg">Harga &amp; Kuantitas</h2>
              <p class="text-xs text-fg-muted">Penentuan harga jual dan satuan takaran</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Field label="Harga Produk (Rp)" required helper="Otomatis terformat dalam Rupiah">
              <div class="relative flex items-center">
                <span class="absolute left-3 text-sm font-bold text-fg-muted select-none">Rp</span>
                <Input
                  v-model="displayPrice"
                  type="text"
                  placeholder="65.000"
                  class="pl-9 font-semibold text-fg"
                />
              </div>
            </Field>

            <Field label="Satuan Kuantitas" required>
              <Select v-model="form.unit_id">
                <option v-for="unit in (units || [])" :key="unit.id" :value="unit.id">
                  {{ unit.name }} {{ unit.symbol ? `(${unit.symbol})` : '' }}
                </option>
              </Select>
            </Field>
          </div>
        </div>

        <div class="rounded-2xl border border-border/80 bg-white p-5 md:p-6 shadow-xs space-y-5">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="MapPin" :size="18" />
            </span>
            <div>
              <h2 class="text-base font-bold text-fg">Asal Usul &amp; Petani Producer</h2>
              <p class="text-xs text-fg-muted">Kemitraan petani dan lokasi kawasan transmigrasi</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <Field label="Petani / Kelompok Tani Mitra" required>
              <Select v-model="form.farmer_id">
                <option v-for="farmer in (farmers || [])" :key="farmer.id" :value="farmer.id">
                  {{ farmer.name }}
                </option>
              </Select>
            </Field>

            <Field label="Kawasan Transmigrasi / Wilayah" required>
              <Select v-model="form.region_id">
                <option v-for="reg in (regions || [])" :key="reg.id" :value="reg.id">
                  {{ reg.name }}
                </option>
              </Select>
            </Field>
          </div>
        </div>
      </div>

      <div class="space-y-6">
        <div class="rounded-2xl border border-border/80 bg-white p-5 md:p-6 shadow-xs space-y-4">
          <div class="flex items-center justify-between border-b border-border/60 pb-3">
            <div class="flex items-center gap-2.5">
              <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
                <Icon :icon="ImageIcon" :size="18" />
              </span>
              <div>
                <h2 class="text-base font-bold text-fg">Foto &amp; Galeri Produk</h2>
                <p class="text-xs text-fg-muted">Gambar 1 otomatis menjadi Cover Utama</p>
              </div>
            </div>
            <span class="text-xs font-semibold text-brand bg-brand-weak px-2.5 py-1 rounded-full">
              {{ galleryImages.length }} Foto
            </span>
          </div>

          <div class="space-y-4">
            <div v-if="galleryImages.length > 0" class="relative overflow-hidden rounded-2xl border-2 border-brand bg-muted/20 aspect-4/3 group shadow-xs">
              <img
                :src="galleryImages[0].url"
                alt="Cover Utama Produk"
                class="size-full object-cover transition-transform duration-300 group-hover:scale-105"
              />
              <div class="absolute top-3 left-3 z-10">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand px-3 py-1 text-xs font-extrabold text-white shadow-md">
                  <Icon :icon="Star" :size="13" class="fill-current text-amber-300" />
                  <span>Gambar 1 (Cover Utama)</span>
                </span>
              </div>

              <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                <label class="px-3 py-1.5 rounded-xl bg-white text-xs font-semibold text-fg shadow-md cursor-pointer inline-flex items-center gap-1.5 hover:bg-muted">
                  <Icon :icon="Upload" :size="14" />
                  <span>Tambah Foto</span>
                  <input type="file" multiple accept="image/*" class="sr-only" @change="handleMultipleImageUpload" />
                </label>
              </div>
            </div>

            <div class="space-y-2">
              <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-fg">Galeri Foto</span>
                <span class="text-[11px] text-fg-muted">Klik ★ untuk set Cover</span>
              </div>

              <div class="grid grid-cols-3 gap-2.5">
                <div
                  v-for="(img, index) in galleryImages"
                  :key="img.id"
                  class="relative group aspect-square overflow-hidden rounded-xl border bg-muted/30 transition-all"
                  :class="index === 0 ? 'border-brand ring-2 ring-brand/30' : 'border-border/80 hover:border-brand/60'"
                >
                  <img :src="img.url" alt="Foto produk" class="size-full object-cover" />

                  <div
                    v-if="index === 0"
                    class="absolute top-1 left-1 rounded bg-brand px-1.5 py-0.5 text-[9px] font-extrabold text-white shadow-xs"
                  >
                    Cover
                  </div>

                  <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-1.5 p-1">
                    <button
                      v-if="index !== 0"
                      type="button"
                      class="flex size-7 items-center justify-center rounded-lg bg-white/90 text-brand shadow-xs transition-transform hover:scale-110 hover:bg-white cursor-pointer"
                      title="Jadikan Cover Utama (Gambar 1)"
                      @click="setAsCover(index)"
                    >
                      <Icon :icon="Star" :size="14" />
                    </button>
                    <button
                      type="button"
                      class="flex size-7 items-center justify-center rounded-lg bg-white/90 text-danger shadow-xs transition-transform hover:scale-110 hover:bg-white cursor-pointer"
                      title="Hapus Foto"
                      @click="removeImage(index)"
                    >
                      <Icon :icon="Trash2" :size="14" />
                    </button>
                  </div>
                </div>

                <label class="flex aspect-square flex-col items-center justify-center gap-1 rounded-xl border-2 border-dashed border-border/90 bg-muted/20 p-2 text-center transition-colors hover:border-brand hover:bg-brand-weak/30 cursor-pointer">
                  <span class="flex size-7 items-center justify-center rounded-full bg-brand-weak text-brand">
                    <Icon :icon="Plus" :size="16" />
                  </span>
                  <span class="text-[11px] font-bold text-fg-muted">Tambah</span>
                  <input type="file" multiple accept="image/*" class="sr-only" @change="handleMultipleImageUpload" />
                </label>
              </div>
            </div>
          </div>
        </div>

        <div class="rounded-2xl border border-border/80 bg-white p-5 md:p-6 shadow-xs space-y-5">
          <div class="flex items-center gap-2.5 border-b border-border/60 pb-3">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-weak text-brand">
              <Icon :icon="Sparkles" :size="18" />
            </span>
            <div>
              <h2 class="text-base font-bold text-fg">Status &amp; Penandaan</h2>
              <p class="text-xs text-fg-muted">Pengaturan ketersediaan &amp; tampilan di beranda</p>
            </div>
          </div>

          <div class="space-y-4">
            <template v-for="(toggle, idx) in toggleFields" :key="toggle.key">
              <div v-if="idx > 0" class="h-px bg-border/60" />
              <label class="flex items-start justify-between gap-3 cursor-pointer select-none">
                <div>
                  <span class="text-xs font-bold text-fg block">{{ toggle.label }}</span>
                  <span class="text-[11px] text-fg-muted block">{{ toggle.description }}</span>
                </div>
                <input
                  type="checkbox"
                  v-model="form[toggle.key]"
                  class="size-4 rounded border-border text-brand accent-brand cursor-pointer mt-0.5"
                />
              </label>
            </template>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>
