import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { axe } from '@/test/a11y'
import RegionCard from './RegionCard.vue'
import type { RegionCard as RegionCardType } from '@/types/home'

const LinkStub = {
  name: 'Link',
  props: ['href'],
  template: '<a :href="href"><slot /></a>',
}

const base: RegionCardType = {
  name: 'Elikobel',
  slug: 'elikobel',
  cover: { thumb: 'https://x/t.jpg', card: 'https://x/c.jpg' },
  villages_count: 5,
  farmer_groups_count: 3,
  products_count: 12,
}

function mountCard(region: RegionCardType) {
  return mount(RegionCard, {
    props: { region },
    attachTo: document.body,
    global: { stubs: { Link: LinkStub } },
  })
}

describe('RegionCard', () => {
  it('menampilkan nama wilayah', () => {
    expect(mountCard(base).text()).toContain('Elikobel')
  })

  it('menampilkan tiga statistik dari data (produk, kelompok, desa)', () => {
    const t = mountCard(base).text()
    expect(t).toContain('Produk')
    expect(t).toContain('12')
    expect(t).toContain('Kelompok')
    expect(t).toContain('3')
    expect(t).toContain('Desa')
    expect(t).toContain('5')
  })

  it('menaut ke detail wilayah via slug', () => {
    expect(mountCard(base).get('a').attributes('href')).toBe('/wilayah/elikobel')
  })

  it('placeholder saat cover null', () => {
    expect(mountCard({ ...base, cover: null }).find('img').exists()).toBe(false)
  })

  it('tanpa pelanggaran a11y', async () => {
    const w = mountCard(base)
    expect(await axe(w.element)).toHaveNoViolations()
    w.unmount()
  })
})
