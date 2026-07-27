<script setup lang="ts">
import { Menu } from '@lucide/vue'
import { Icon, Breadcrumb } from '@/Components/ui'
import AdminUserProfile from './AdminUserProfile.vue'

defineProps<{
  title?: string
  subtitle?: string
}>()

defineEmits<{
  (e: 'toggle-sidebar'): void
}>()
</script>

<template>
  <header
    class="sticky top-0 z-30 flex h-14 w-full items-center justify-between border-b border-border/80 bg-white/90 px-4 backdrop-blur-xl md:px-6"
  >
    <div class="flex items-center gap-4">
      <button
        type="button"
        class="flex size-9 items-center justify-center rounded-xl border border-border/80 text-fg-muted transition-colors hover:bg-muted/60 hover:text-fg lg:hidden"
        aria-label="Buka navigasi"
        @click="$emit('toggle-sidebar')"
      >
        <Icon :icon="Menu" :size="18" />
      </button>
      <Breadcrumb
        :items="[
          { label: 'Admin', href: '/admin/dashboard' },
          ...(title ? [{ label: title }] : [])
        ]"
      />
    </div>

    <div class="flex items-center gap-3">
      <AdminUserProfile />
    </div>
  </header>
</template>
