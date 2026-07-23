import { describe, it, expect } from 'vitest'
import { defineComponent, h } from 'vue'
import { mount } from '@vue/test-utils'
import { axe } from '@/test/a11y'
import Field from './Field.vue'
import Input from './Input.vue'

function mountField(props: Record<string, unknown>) {
  const Wrapper = defineComponent({
    setup() {
      return () =>
        h(Field, props, {
          default: () => h(Input, { placeholder: 'Tulis...' }),
        })
    },
  })
  return mount(Wrapper, { attachTo: document.body })
}

describe('Field + Input', () => {
  it('mengaitkan label ke input via for/id', () => {
    const w = mountField({ label: 'Nama Produk' })
    const label = w.get('label')
    const input = w.get('input')
    expect(label.attributes('for')).toBe(input.attributes('id'))
    expect(input.attributes('id')).toBeTruthy()
  })

  it('menandai wajib pada label', () => {
    const w = mountField({ label: 'Nama Produk', required: true })
    expect(w.get('label').text()).toContain('*')
    expect(w.get('input').attributes('aria-required')).toBe('true')
  })

  it('helper terhubung via aria-describedby', () => {
    const w = mountField({ label: 'Harga', helper: 'Dalam rupiah' })
    const input = w.get('input')
    const helper = w.get('p')
    expect(input.attributes('aria-describedby')).toBe(helper.attributes('id'))
  })

  it('error → aria-invalid, role=alert, describedby menunjuk error', () => {
    const w = mountField({ label: 'Email', error: 'Email tidak valid' })
    const input = w.get('input')
    const alert = w.get('[role="alert"]')
    expect(input.attributes('aria-invalid')).toBe('true')
    expect(input.attributes('aria-describedby')).toContain(alert.attributes('id') as string)
    expect(alert.text()).toBe('Email tidak valid')
  })

  it('tanpa pelanggaran a11y (dengan label, helper, error)', async () => {
    const w = mountField({ label: 'Nama', helper: 'Maks 120 karakter', error: 'Wajib diisi' })
    expect(await axe(w.element)).toHaveNoViolations()
    w.unmount()
  })
})
