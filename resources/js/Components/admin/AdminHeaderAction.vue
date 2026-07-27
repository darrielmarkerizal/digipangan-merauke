<script setup lang="ts">
import type { Component } from 'vue'
import { Button, Icon } from '@/Components/ui'

withDefaults(
  defineProps<{
    label: string
    icon?: Component
    variant?: 'primary' | 'secondary' | 'danger' | 'ghost'
  }>(),
  {
    variant: 'primary',
  }
)

defineEmits<{
  (e: 'click'): void
}>()
</script>

<template>
  <Button
    size="sm"
    :variant="variant"
    class="gap-2 shadow-xs"
    @click="$emit('click')"
  >
    <Icon v-if="icon" :icon="icon" :size="15" />
    <span>{{ label }}</span>
  </Button>
</template>
