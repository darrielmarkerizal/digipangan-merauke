<script setup lang="ts">
import { inject } from 'vue'
import { FIELD_KEY } from './field-context'

defineOptions({ inheritAttrs: false })

withDefaults(
  defineProps<{ modelValue?: string | number; type?: string }>(),
  { type: 'text' },
)
const emit = defineEmits<{ 'update:modelValue': [value: string] }>()

const field = inject(FIELD_KEY, null)

const base =
  'w-full min-h-11 rounded-control border border-border bg-card px-3 text-base text-fg placeholder:text-fg-muted aria-invalid:border-danger disabled:opacity-60'
</script>

<template>
  <input
    :id="field?.id.value"
    :type="type"
    :value="modelValue"
    :aria-invalid="field?.invalid.value || undefined"
    :aria-describedby="field?.describedBy.value"
    :aria-required="field?.required.value || undefined"
    :class="base"
    v-bind="$attrs"
    @input="emit('update:modelValue', ($event.target as HTMLInputElement).value)"
  />
</template>
