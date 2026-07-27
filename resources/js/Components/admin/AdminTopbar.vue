<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Menu, ExternalLink } from '@lucide/vue'
import { Icon } from '@/Components/ui'

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

      <div>
        <div class="flex items-center gap-2">
          <span class="text-xs font-semibold uppercase tracking-wider text-brand">
            Portal Administrasi
          </span>
          <span class="text-xs text-border/80">•</span>
          <span class="text-xs font-medium text-fg-muted">
            DigiPangan Merauke
          </span>
        </div>
        <h1 v-if="title" class="text-base font-bold tracking-tight text-fg">
          {{ title }}
        </h1>
      </div>
    </div>

    <div class="flex items-center gap-3">
      <Link
        href="/"
        class="inline-flex items-center gap-2 rounded-xl border border-border/80 bg-white px-3.5 py-1.5 text-xs font-bold text-fg shadow-xs transition-colors hover:border-brand/40 hover:bg-bg"
      >
        <span>Situs Publik</span>
        <Icon :icon="ExternalLink" :size="14" />
      </Link>
    </div>
  </header>
</template>
