const rupiahFormatter = new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  minimumFractionDigits: 0,
  maximumFractionDigits: 0,
})

const angkaFormatter = new Intl.NumberFormat('id-ID')

const tanggalFormatter = new Intl.DateTimeFormat('id-ID', {
  day: 'numeric',
  month: 'long',
  year: 'numeric',
})

export function formatRupiah(value: number): string {
  return rupiahFormatter.format(value).replace(/\s+/g, ' ').trim()
}

export function formatAngka(value: number): string {
  return angkaFormatter.format(value)
}

export function formatTanggal(value: Date | string): string {
  const date = value instanceof Date ? value : new Date(value)
  if (Number.isNaN(date.getTime())) return typeof value === 'string' ? value : ''
  return tanggalFormatter.format(date)
}
