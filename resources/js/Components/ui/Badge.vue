<script setup lang="ts">
import { type Component } from 'vue'
import { tv, type VariantProps } from 'tailwind-variants'
import Icon from './Icon.vue'

const badge = tv({
  base: 'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-sm font-medium leading-none',
  variants: {
    variant: {
      neutral: 'bg-muted text-fg-muted',
      brand: 'bg-brand-weak text-brand',
      success: 'bg-success-weak text-success',
      danger: 'bg-danger-weak text-danger',
      warning: 'bg-warning-weak text-warning',
      info: 'bg-info-weak text-info',
      unggulan: 'bg-accent-fill text-fg',
    },
  },
  defaultVariants: { variant: 'neutral' },
})

type BadgeVariants = VariantProps<typeof badge>

withDefaults(
  defineProps<{
    variant?: BadgeVariants['variant']
    icon?: Component
  }>(),
  { variant: 'neutral' },
)
</script>

<template>
  <span :class="badge({ variant })">
    <Icon v-if="icon" :icon="icon" :size="16" />
    <slot />
  </span>
</template>
