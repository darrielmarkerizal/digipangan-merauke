<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ArrowLeft, Edit2, MapPin, CheckCircle2, XCircle, Info, Map } from '@lucide/vue'
import { Button, Icon, Badge } from '@/Components/ui'

const props = defineProps<{
  village: any
}>()
</script>

<template>
  <AdminLayout
    :title="`Detail Desa: ${village.name}`"
    subtitle="Informasi lengkap mengenai desa ini."
  >
    <template #actions>
      <Link :href="`/admin/desa/${village.id}/edit`">
        <Button size="sm" class="gap-1.5 font-semibold">
          <Icon :icon="Edit2" :size="16" />
          <span>Edit Desa</span>
        </Button>
      </Link>
    </template>

    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <Link
          href="/admin/desa"
          class="inline-flex items-center gap-2 text-sm font-medium text-fg-muted transition-colors hover:text-fg"
        >
          <Icon :icon="ArrowLeft" :size="16" />
          <span>Kembali ke Daftar Desa</span>
        </Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
          <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-6">
            <div class="flex items-center gap-2 border-b border-border/60 pb-3">
              <Icon :icon="Info" :size="18" class="text-brand" />
              <h3 class="text-sm font-bold text-fg">Profil Dasar</h3>
            </div>

            <div class="space-y-5">
              <div>
                <span class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1">Nama Desa</span>
                <span class="text-sm font-medium text-fg">{{ village.name }}</span>
              </div>
              
              <div>
                <span class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1">Tergabung pada Wilayah / Distrik</span>
                <Link v-if="village.region" :href="`/admin/wilayah/${village.region.id}`" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand hover:underline">
                  <Icon :icon="Map" :size="14" />
                  <span>{{ village.region.name }}</span>
                </Link>
                <span v-else class="text-sm font-medium text-fg-muted">-</span>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
          <div class="rounded-2xl border border-border/80 bg-white shadow-xs overflow-hidden">
            <div class="p-6 border-b border-border/60">
              <div class="flex items-center gap-2">
                <Icon :icon="MapPin" :size="18" class="text-brand" />
                <h3 class="text-sm font-bold text-fg">Status</h3>
              </div>
            </div>
            <div class="p-6 space-y-5">
              <div>
                <span class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1">Status Desa</span>
                <Badge :variant="village.is_active ? 'success' : 'neutral'" class="gap-1 mt-1">
                  <Icon :icon="village.is_active ? CheckCircle2 : XCircle" :size="12" />
                  <span>{{ village.is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
                </Badge>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
