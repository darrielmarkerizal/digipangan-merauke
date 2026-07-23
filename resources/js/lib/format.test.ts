import { describe, it, expect } from 'vitest'
import { formatRupiah, formatAngka, formatTanggal } from '@/lib/format'

describe('formatRupiah', () => {
  it('memformat tanpa desimal + pemisah ribuan id-ID', () => {
    expect(formatRupiah(45000)).toBe('Rp 45.000')
    expect(formatRupiah(0)).toBe('Rp 0')
    expect(formatRupiah(1250000)).toBe('Rp 1.250.000')
  })
})

describe('formatAngka', () => {
  it('memberi pemisah ribuan id-ID', () => {
    expect(formatAngka(12345)).toBe('12.345')
    expect(formatAngka(7)).toBe('7')
  })
})

describe('formatTanggal', () => {
  it('memformat ke gaya Indonesia', () => {
    expect(formatTanggal('2026-07-22')).toBe('22 Juli 2026')
    expect(formatTanggal(new Date('2026-01-01'))).toBe('1 Januari 2026')
  })

  it('mengembalikan string asli bila bukan tanggal valid', () => {
    expect(formatTanggal('bukan-tanggal')).toBe('bukan-tanggal')
  })
})
