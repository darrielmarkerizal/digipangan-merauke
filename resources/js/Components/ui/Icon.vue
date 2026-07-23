<script setup lang="ts">
import { computed, type Component } from 'vue'

interface Props {
  icon: Component
  size?: number
  strokeWidth?: number
  label?: string
}

const props = withDefaults(defineProps<Props>(), {
  size: 24,
  strokeWidth: 1.75,
})

const a11yAttrs = computed(() =>
  props.label
    ? { role: 'img', 'aria-label': props.label }
    : { 'aria-hidden': 'true' },
)
</script>

<template>
  <component
    :is="icon"
    :size="size"
    :stroke-width="strokeWidth"
    :absolute-stroke-width="true"
    focusable="false"
    v-bind="a11yAttrs"
  />
</template>
