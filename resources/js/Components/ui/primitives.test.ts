import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { CircleCheck } from '@lucide/vue'
import { axe } from '@/test/a11y'

import Icon from './Icon.vue'
import Spinner from './Spinner.vue'
import Skeleton from './Skeleton.vue'
import Badge from './Badge.vue'
import Chip from './Chip.vue'
import Card from './Card.vue'
import EmptyState from './EmptyState.vue'
import ErrorState from './ErrorState.vue'

describe('Icon', () => {
  it('dekoratif secara default (aria-hidden)', () => {
    const w = mount(Icon, { props: { icon: CircleCheck } })
    expect(w.attributes('aria-hidden')).toBe('true')
    expect(w.attributes('role')).toBeUndefined()
    expect(w.attributes('stroke-width')).toBe('1.75')
  })

  it('bermakna saat diberi label (role=img + aria-label)', () => {
    const w = mount(Icon, { props: { icon: CircleCheck, label: 'Tersedia' } })
    expect(w.attributes('role')).toBe('img')
    expect(w.attributes('aria-label')).toBe('Tersedia')
    expect(w.attributes('aria-hidden')).toBeUndefined()
  })
})

describe('Spinner', () => {
  it('mengumumkan status memuat', () => {
    const w = mount(Spinner)
    expect(w.attributes('role')).toBe('status')
    expect(w.attributes('aria-label')).toBe('Memuat')
  })
})

describe('Skeleton', () => {
  it('aria-hidden dan memakai radius yang dipilih', () => {
    const w = mount(Skeleton, { props: { radius: 'card' } })
    expect(w.attributes('aria-hidden')).toBe('true')
    expect(w.classes()).toContain('rounded-card')
  })
})

describe('Badge', () => {
  it('menerapkan kelas varian', () => {
    const w = mount(Badge, { props: { variant: 'success' }, slots: { default: 'Tersedia' } })
    expect(w.classes()).toContain('text-success')
    expect(w.text()).toBe('Tersedia')
  })

  it('tanpa pelanggaran a11y', async () => {
    const w = mount(Badge, {
      props: { variant: 'success', icon: CircleCheck },
      slots: { default: 'Tersedia' },
    })
    expect(await axe(w.element)).toHaveNoViolations()
  })
})

describe('Chip', () => {
  it('mencerminkan status aktif via aria-pressed + kelas', () => {
    const w = mount(Chip, { props: { active: true }, slots: { default: 'Sayuran' } })
    expect(w.attributes('aria-pressed')).toBe('true')
    expect(w.classes()).toContain('bg-brand-weak')
  })

  it('memiliki target sentuh >=44px (min-h-11)', () => {
    const w = mount(Chip, { slots: { default: 'Buah' } })
    expect(w.classes()).toContain('min-h-11')
  })

  it('tanpa pelanggaran a11y', async () => {
    const w = mount(Chip, { slots: { default: 'Sayuran' }, attachTo: document.body })
    expect(await axe(w.element)).toHaveNoViolations()
    w.unmount()
  })
})

describe('Card', () => {
  it('interaktif menambah umpan hover/press', () => {
    const w = mount(Card, { props: { interactive: true }, slots: { default: 'x' } })
    expect(w.classes()).toContain('hover:shadow-soft')
    expect(w.classes()).toContain('active:scale-[0.98]')
  })

  it('dapat dirender sebagai elemen lain via `as`', () => {
    const w = mount(Card, { props: { as: 'article' }, slots: { default: 'x' } })
    expect(w.element.tagName).toBe('ARTICLE')
  })
})

describe('EmptyState', () => {
  it('menampilkan judul + deskripsi dan tanpa pelanggaran a11y', async () => {
    const w = mount(EmptyState, {
      props: { title: 'Belum ada produk', description: 'Coba kategori lain.' },
      attachTo: document.body,
    })
    expect(w.text()).toContain('Belum ada produk')
    expect(await axe(w.element)).toHaveNoViolations()
    w.unmount()
  })
})

describe('ErrorState', () => {
  it('memakai role=alert dengan teks default', async () => {
    const w = mount(ErrorState, { attachTo: document.body })
    expect(w.attributes('role')).toBe('alert')
    expect(w.text()).toContain('Gagal memuat')
    expect(await axe(w.element)).toHaveNoViolations()
    w.unmount()
  })
})
