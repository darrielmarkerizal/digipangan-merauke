<script setup lang="ts">
import { LoaderCircle } from '@lucide/vue'
import Icon from './Icon.vue'

withDefaults(defineProps<{ size?: number; label?: string }>(), {
  size: 20,
  label: 'Memuat',
})
</script>

<template>
  <span role="status" :aria-label="label" class="inline-flex text-current">
    <Icon :icon="LoaderCircle" :size="size" class="motion-safe:animate-spin" />
  </span>
</template>
