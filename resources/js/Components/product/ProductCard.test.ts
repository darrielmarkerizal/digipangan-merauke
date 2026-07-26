import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import { axe } from '@/test/a11y'
import ProductCard from './ProductCard.vue'
import type { ProductCard as ProductCardType } from '@/types/home'

const LinkStub = {
  name: 'Link',
  props: ['href'],
  template: '<a :href="href"><slot /></a>',
}

const base: ProductCardType = {
  name: 'Cabai Rawit Segar',
  slug: 'cabai-rawit-segar',
  price: '45000.00',
  stock_available: true,
  photo: { thumb: 'https://x/t.jpg', card: 'https://x/c.jpg' },
  region: { name: 'Elikobel', slug: 'elikobel' },
  farmer: { name: 'Bapak Muhamad Riam', slug: 'muhamad-riam' },
}

function mountCard(product: ProductCardType) {
  return mount(ProductCard, {
    props: { product },
    attachTo: document.body,
    global: { stubs: { Link: LinkStub } },
  })
}

describe('ProductCard', () => {
  it('memformat harga ke rupiah tabular', () => {
    expect(mountCard(base).text()).toContain('Rp 45.000')
  })

  it('menaut ke detail via slug', () => {
    expect(mountCard(base).get('a').attributes('href')).toBe('/produk/cabai-rawit-segar')
  })

  it('tidak menampilkan badge stok saat produk tersedia', () => {
    expect(mountCard(base).text()).not.toContain('Stok habis')
  })

  it('menampilkan badge saat stok kosong', () => {
    expect(mountCard({ ...base, stock_available: false }).text()).toContain(
      'Stok habis',
    )
  })

  it('varian spotlight menampilkan nama petani', () => {
    const w = mount(ProductCard, {
      props: { product: base, variant: 'spotlight' },
      attachTo: document.body,
      global: { stubs: { Link: LinkStub } },
    })
    expect(w.text()).toContain('Bapak Muhamad Riam')
  })

  it('memberi alt deskriptif + lazy pada foto', () => {
    const w = mountCard(base)
    expect(w.get('img').attributes('alt')).toBe('Cabai Rawit Segar dari Elikobel')
    expect(w.get('img').attributes('loading')).toBe('lazy')
  })

  it('menampilkan placeholder saat foto null', () => {
    expect(mountCard({ ...base, photo: null }).find('img').exists()).toBe(false)
  })

  it('tanpa pelanggaran a11y', async () => {
    const w = mountCard(base)
    expect(await axe(w.element)).toHaveNoViolations()
    w.unmount()
  })
})
