<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ArrowLeft, Users, MapPin, Edit2, Calendar } from '@lucide/vue'
import { Button, Icon, EmptyState } from '@/Components/ui'
import { formatTanggal } from '@/lib/format'

const props = defineProps<{
  farmerGroup: any
  members: any[]
}>()
</script>

<template>
  <AdminLayout
    :title="`Detail Kelompok Tani: ${farmerGroup.name}`"
    subtitle="Informasi lengkap mengenai profil dan daftar anggota kelompok tani."
  >
    <template #actions>
      <Link :href="`/admin/kelompok-tani/${farmerGroup.id}/edit`">
        <Button size="sm" class="gap-1.5 font-semibold">
          <Icon :icon="Edit2" :size="16" />
          <span>Edit Kelompok Tani</span>
        </Button>
      </Link>
    </template>

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

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
          <div class="rounded-2xl border border-border/80 bg-white p-6 shadow-xs space-y-6">
            <div class="flex items-center gap-2 border-b border-border/60 pb-3">
              <Icon :icon="MapPin" :size="18" class="text-brand" />
              <h3 class="text-sm font-bold text-fg">Informasi Kelompok Tani</h3>
            </div>

            <div class="space-y-5">
              <div>
                <span class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1">Nama Kelompok Tani</span>
                <span class="text-sm font-medium text-fg">{{ farmerGroup.name }}</span>
              </div>
              
              <div>
                <span class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1">Distrik / Kawasan</span>
                <span class="text-sm font-medium text-fg">{{ farmerGroup.region?.name || '-' }}</span>
              </div>

              <div>
                <span class="block text-xs font-semibold text-fg-muted uppercase tracking-wider mb-1">Dibuat Pada</span>
                <div class="flex items-center gap-1.5 text-sm font-medium text-fg">
                  <Icon :icon="Calendar" :size="14" class="text-fg-muted" />
                  <span>{{ farmerGroup.created_at ? formatTanggal(farmerGroup.created_at) : '-' }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
          <div class="rounded-2xl border border-border/80 bg-white shadow-xs overflow-hidden">
            <div class="p-6 border-b border-border/60 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
              <div class="flex items-center gap-3">
                <div class="flex size-10 items-center justify-center rounded-xl bg-brand-weak/50 text-brand">
                  <Icon :icon="Users" :size="20" />
                </div>
                <div>
                  <h3 class="text-base font-bold text-fg">Daftar Anggota Petani</h3>
                  <p class="text-xs text-fg-muted">Petani yang tergabung di kelompok tani ini.</p>
                </div>
              </div>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left text-sm text-fg">
                <thead class="border-b border-border/60 bg-muted/30 text-xs font-bold text-fg-muted uppercase tracking-wider">
                  <tr>
                    <th scope="col" class="px-6 py-3.5">Nama Petani</th>
                    <th scope="col" class="px-6 py-3.5">No. Telepon</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                  <tr v-if="members.length === 0">
                    <td colspan="2" class="px-6 py-12 text-center">
                      <EmptyState
                        title="Belum ada anggota"
                        description="Kelompok tani ini belum memiliki anggota."
                        :icon="Users"
                      />
                    </td>
                  </tr>
                  <tr v-for="member in members" :key="member.id" class="hover:bg-muted/20 transition-colors">
                    <td class="px-6 py-4">
                      <Link :href="`/admin/petani/${member.id}`" class="font-bold text-fg hover:text-brand hover:underline transition-colors block">
                        {{ member.name }}
                      </Link>
                      <div class="text-xs text-fg-muted mt-0.5">ID: #PTN-00{{ member.id }}</div>
                    </td>
                    <td class="px-6 py-4 font-medium text-fg-muted">
                      {{ member.phone }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
