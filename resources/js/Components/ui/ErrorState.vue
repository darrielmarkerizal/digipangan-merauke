<script setup lang="ts">
import { TriangleAlert } from '@lucide/vue'
import Icon from './Icon.vue'

withDefaults(
  defineProps<{
    title?: string
    description?: string
  }>(),
  {
    title: 'Gagal memuat',
    description: 'Periksa koneksi Anda lalu coba lagi.',
  },
)
</script>

<template>
  <div
    role="alert"
    class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center"
  >
    <span class="flex size-12 items-center justify-center rounded-full bg-danger-weak text-danger">
      <Icon :icon="TriangleAlert" :size="24" />
    </span>
    <div class="space-y-1">
      <p class="text-lg font-semibold text-fg">{{ title }}</p>
      <p v-if="description" class="mx-auto max-w-sm text-sm text-fg-muted">
        {{ description }}
      </p>
    </div>
    <div v-if="$slots.action" class="mt-1">
      <slot name="action" />
    </div>
  </div>
</template>
