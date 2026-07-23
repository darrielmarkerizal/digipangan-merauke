<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import {
  Search,
  Phone,
  MapPin,
  CircleCheck,
  CircleX,
  Sprout,
} from '@lucide/vue'
import {
  Icon,
  Button,
  Badge,
  Chip,
  Card,
  Skeleton,
  Spinner,
  EmptyState,
  ErrorState,
  Field,
  Input,
  Textarea,
  Select,
  Accordion,
  AccordionItem,
  AlertDialog,
} from '@/Components/ui'

const activeChip = ref('sayuran')
const nama = ref('')
const deskripsi = ref('')
const kategori = ref('')

const kategoriChips = [
  { slug: 'sayuran', label: 'Sayuran' },
  { slug: 'buah', label: 'Buah-buahan' },
  { slug: 'kebun', label: 'Hasil Perkebunan' },
]
</script>

<template>
  <Head title="Design System" />

  <main class="mx-auto max-w-4xl space-y-12 px-4 py-10">
    <header class="space-y-1">
      <h1 class="text-3xl font-bold text-fg">DigiPangan Design System</h1>
      <p class="text-fg-muted">Living style guide komponen UI dasar.</p>
    </header>

    <section class="space-y-4">
      <h2 class="text-xl font-bold text-fg">Tombol</h2>
      <div class="flex flex-wrap items-center gap-3">
        <Button>Lihat Produk</Button>
        <Button variant="secondary">Sekunder</Button>
        <Button variant="ghost">Ghost</Button>
        <Button variant="whatsapp"><Icon :icon="Phone" :size="18" /> WhatsApp</Button>
        <Button variant="danger">Hapus</Button>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <Button size="sm">Kecil</Button>
        <Button size="md">Sedang</Button>
        <Button size="lg">Besar</Button>
        <Button :loading="true">Menyimpan</Button>
        <Button disabled>Nonaktif</Button>
        <Button icon-only aria-label="Cari"><Icon :icon="Search" :size="20" /></Button>
      </div>
      <Button variant="whatsapp" full-width>
        <Icon :icon="Phone" :size="20" /> Hubungi Penjual
      </Button>
    </section>

    <section class="space-y-4">
      <h2 class="text-xl font-bold text-fg">Badge</h2>
      <div class="flex flex-wrap gap-2">
        <Badge variant="success" :icon="CircleCheck">Tersedia</Badge>
        <Badge variant="danger" :icon="CircleX">Stok habis</Badge>
        <Badge variant="unggulan">Unggulan</Badge>
        <Badge variant="brand">Sayuran</Badge>
        <Badge variant="neutral">Netral</Badge>
      </div>
    </section>

    <section class="space-y-4">
      <h2 class="text-xl font-bold text-fg">Chip filter</h2>
      <div class="flex flex-wrap gap-2">
        <Chip
          v-for="c in kategoriChips"
          :key="c.slug"
          :active="activeChip === c.slug"
          @click="activeChip = c.slug"
        >
          {{ c.label }}
        </Chip>
      </div>
    </section>

    <section class="space-y-4">
      <h2 class="text-xl font-bold text-fg">Kartu & Skeleton</h2>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <Card interactive :padding="'none'">
          <div class="aspect-[4/3] rounded-t-card bg-muted"></div>
          <div class="space-y-1 p-4">
            <p class="font-semibold text-fg">Cabai Rawit Segar</p>
            <p class="flex items-center gap-1 text-sm text-fg-muted">
              <Icon :icon="MapPin" :size="16" /> Elikobel
            </p>
            <p class="pt-1 text-lg font-bold tabular-nums text-fg">Rp 45.000</p>
          </div>
        </Card>
        <Card :padding="'none'">
          <Skeleton radius="none" class="aspect-[4/3] rounded-t-card" />
          <div class="space-y-2 p-4">
            <Skeleton class="h-4 w-2/3" />
            <Skeleton class="h-4 w-1/3" />
            <Skeleton class="h-6 w-1/2" />
          </div>
        </Card>
      </div>
    </section>

    <section class="space-y-4">
      <h2 class="text-xl font-bold text-fg">State data</h2>
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <Card>
          <EmptyState
            :icon="Sprout"
            title="Belum ada produk"
            description="Belum ada produk pada kategori ini."
          >
            <template #action>
              <Button variant="secondary">Lihat semua produk</Button>
            </template>
          </EmptyState>
        </Card>
        <Card>
          <ErrorState>
            <template #action>
              <Button variant="secondary">Coba lagi</Button>
            </template>
          </ErrorState>
        </Card>
      </div>
      <div class="flex items-center gap-2 text-fg-muted">
        <Spinner /> Memuat data...
      </div>
    </section>

    <section class="space-y-4">
      <h2 class="text-xl font-bold text-fg">Form</h2>
      <div class="max-w-md space-y-4">
        <Field label="Nama produk" required helper="Maksimal 120 karakter">
          <Input v-model="nama" placeholder="Cabai rawit segar" />
        </Field>
        <Field label="Kategori" required>
          <Select v-model="kategori">
            <option value="" disabled>Pilih kategori</option>
            <option value="sayuran">Sayuran</option>
            <option value="buah">Buah-buahan</option>
          </Select>
        </Field>
        <Field label="Deskripsi" error="Deskripsi wajib diisi.">
          <Textarea v-model="deskripsi" placeholder="Tulis deskripsi..." />
        </Field>
      </div>
    </section>

    <section class="space-y-4">
      <h2 class="text-xl font-bold text-fg">FAQ (Accordion)</h2>
      <Accordion default-value="a">
        <AccordionItem value="a" title="Apa itu DigiPangan Merauke?">
          Etalase komoditas lokal kawasan transmigrasi Merauke.
        </AccordionItem>
        <AccordionItem value="b" title="Bagaimana cara menghubungi petani?">
          Lewat tombol Hubungi Penjual yang membuka WhatsApp.
        </AccordionItem>
      </Accordion>
    </section>

    <section class="space-y-4">
      <h2 class="text-xl font-bold text-fg">Konfirmasi (AlertDialog)</h2>
      <AlertDialog
        title="Hapus produk ini?"
        description="Tindakan ini tidak dapat dibatalkan."
      >
        <template #trigger>
          <Button variant="danger">Hapus produk</Button>
        </template>
      </AlertDialog>
    </section>
  </main>
</template>
