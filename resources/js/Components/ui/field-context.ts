import type { ComputedRef, InjectionKey } from 'vue'

export interface FieldContext {
  id: ComputedRef<string>
  describedBy: ComputedRef<string | undefined>
  invalid: ComputedRef<boolean>
  required: ComputedRef<boolean>
}

export const FIELD_KEY: InjectionKey<FieldContext> = Symbol('FieldContext')
