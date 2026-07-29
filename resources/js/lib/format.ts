const angkaFormatter = new Intl.NumberFormat('id-ID')

const tanggalFormatter = new Intl.DateTimeFormat('id-ID', {
  day: 'numeric',
  month: 'long',
  year: 'numeric',
})

export function formatRupiah(
  value: number | string | null | undefined,
  withPrefix: boolean = true
): string {
  if (value === null || value === undefined || value === '') return ''
  const num = typeof value === 'number' ? value : parseFloat(String(value))
  if (Number.isNaN(num)) return ''
  const formatted = angkaFormatter.format(Math.floor(num))
  return withPrefix ? `Rp ${formatted}` : formatted
}

export function formatAngka(value: number | string | null | undefined): string {
  if (value === null || value === undefined || value === '') return ''
  const num = typeof value === 'number' ? value : parseFloat(String(value))
  if (Number.isNaN(num)) return ''
  return angkaFormatter.format(num)
}

export function formatTanggal(value: Date | string): string {
  const date = value instanceof Date ? value : new Date(value)
  if (Number.isNaN(date.getTime())) return typeof value === 'string' ? value : ''
  return tanggalFormatter.format(date)
}
