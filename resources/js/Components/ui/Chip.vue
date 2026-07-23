<script setup lang="ts">
import { tv } from 'tailwind-variants'

defineOptions({ inheritAttrs: false })

withDefaults(defineProps<{ active?: boolean }>(), { active: false })

const chip = tv({
  base: [
    'inline-flex min-h-11 items-center gap-1.5 rounded-full border px-4 text-sm font-medium',
    'transition-colors select-none',
  ],
  variants: {
    active: {
      true: 'border-brand bg-brand-weak text-brand',
      false: 'border-border bg-card text-fg-muted hover:bg-muted hover:text-fg',
    },
  },
})
</script>

<template>
  <button
    type="button"
    :class="chip({ active })"
    :aria-pressed="active"
    v-bind="$attrs"
  >
    <slot />
  </button>
</template>
