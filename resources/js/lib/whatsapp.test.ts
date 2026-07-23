import { describe, it, expect } from 'vitest'
import { buildWhatsappUrl } from '@/lib/whatsapp'

describe('buildWhatsappUrl', () => {
  it('menormalkan awalan 0 menjadi 62', () => {
    const url = buildWhatsappUrl('081234567890', 'Cabai Rawit')
    expect(url).toContain('https://wa.me/6281234567890?text=')
  })

  it('menerima format +62 dan E.164', () => {
    expect(buildWhatsappUrl('+6281234567890', 'X')).toContain('wa.me/6281234567890')
    expect(buildWhatsappUrl('6281234567890', 'X')).toContain('wa.me/6281234567890')
  })

  it('menambah 62 bila nomor tanpa awalan negara', () => {
    expect(buildWhatsappUrl('81234567890', 'X')).toContain('wa.me/6281234567890')
  })

  it('meng-encode teks prefilled dengan nama produk', () => {
    const url = buildWhatsappUrl('081234567890', 'Cabai Rawit')!
    expect(decodeURIComponent(url.split('text=')[1])).toBe(
      'Halo, saya tertarik dengan produk "Cabai Rawit" di DigiPangan Merauke.',
    )
  })

  it('mengembalikan null bila nomor kosong/invalid', () => {
    expect(buildWhatsappUrl('', 'X')).toBeNull()
    expect(buildWhatsappUrl('abc', 'X')).toBeNull()
  })
})
