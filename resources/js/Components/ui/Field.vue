<script setup lang="ts">
import { computed, provide, useId } from 'vue'
import Label from './Label.vue'
import { FIELD_KEY } from './field-context'

const props = withDefaults(
  defineProps<{
    label?: string
    helper?: string
    error?: string
    required?: boolean
    id?: string
  }>(),
  { required: false },
)

const uid = useId()
const id = computed(() => props.id ?? `field-${uid}`)
const helperId = computed(() => (props.helper ? `${id.value}-helper` : undefined))
const errorId = computed(() => (props.error ? `${id.value}-error` : undefined))
const invalid = computed(() => Boolean(props.error))
const describedBy = computed(
  () => [errorId.value, helperId.value].filter(Boolean).join(' ') || undefined,
)

provide(FIELD_KEY, {
  id,
  describedBy,
  invalid,
  required: computed(() => props.required),
})
</script>

<template>
  <div class="space-y-1.5">
    <Label v-if="label">{{ label }}</Label>
    <slot :id="id" />
    <p v-if="helper && !error" :id="helperId" class="text-sm text-fg-muted">
      {{ helper }}
    </p>
    <p v-if="error" :id="errorId" role="alert" class="text-sm font-medium text-danger">
      {{ error }}
    </p>
  </div>
</template>
