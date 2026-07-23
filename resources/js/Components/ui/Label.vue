<script setup lang="ts">
import { computed, inject } from 'vue'
import { FIELD_KEY } from './field-context'

const props = defineProps<{ for?: string }>()
const field = inject(FIELD_KEY, null)

const forId = computed(() => props.for ?? field?.id.value)
const required = computed(() => field?.required.value ?? false)
</script>

<template>
  <label :for="forId" class="block text-sm font-medium text-fg">
    <slot />
    <span v-if="required" class="text-danger" aria-hidden="true">&nbsp;*</span>
  </label>
</template>
