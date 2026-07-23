import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import { axe } from '@/test/a11y'
import Button from './Button.vue'

describe('Button', () => {
  it('render <button> dengan varian default primary', () => {
    const w = mount(Button, { slots: { default: 'Lihat Produk' } })
    expect(w.element.tagName).toBe('BUTTON')
    expect(w.attributes('type')).toBe('button')
    expect(w.classes()).toContain('bg-brand')
    expect(w.classes()).toContain('min-h-11')
  })

  it('menerapkan varian secondary/ghost', () => {
    expect(mount(Button, { props: { variant: 'secondary' } }).classes()).toContain('border-border')
    expect(mount(Button, { props: { variant: 'ghost' } }).classes()).toContain('text-brand')
  })

  it('loading → disabled + aria-busy + status spinner', () => {
    const w = mount(Button, { props: { loading: true }, slots: { default: 'Kirim' } })
    expect(w.attributes('disabled')).toBeDefined()
    expect(w.attributes('aria-busy')).toBe('true')
    expect(w.find('[role="status"]').exists()).toBe(true)
  })

  it('meneruskan atribut native (type, aria-label)', () => {
    const w = mount(Button, {
      attrs: { type: 'submit', 'aria-label': 'Simpan' },
      props: { iconOnly: true },
    })
    expect(w.attributes('type')).toBe('submit')
    expect(w.attributes('aria-label')).toBe('Simpan')
  })

  it('memperingatkan iconOnly tanpa nama aksesibel (dev)', () => {
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {})
    mount(Button, { props: { iconOnly: true } })
    expect(warn).toHaveBeenCalledWith(expect.stringContaining('iconOnly'))
    warn.mockRestore()
  })

  it('dapat dirender sebagai elemen lain via `as`', () => {
    const w = mount(Button, { props: { as: 'a' }, attrs: { href: '/produk' }, slots: { default: 'x' } })
    expect(w.element.tagName).toBe('A')
    expect(w.attributes('href')).toBe('/produk')
  })

  it('tanpa pelanggaran a11y', async () => {
    const w = mount(Button, { slots: { default: 'Hubungi Penjual' }, attachTo: document.body })
    expect(await axe(w.element)).toHaveNoViolations()
    w.unmount()
  })
})
