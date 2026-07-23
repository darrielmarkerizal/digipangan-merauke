export function buildWhatsappUrl(phone: string, productName: string): string | null {
  let digits = phone.replace(/\D/g, '')
  if (!digits) return null

  if (digits.startsWith('0')) {
    digits = `62${digits.slice(1)}`
  } else if (!digits.startsWith('62')) {
    digits = `62${digits}`
  }

  const text = `Halo, saya tertarik dengan produk "${productName}" di DigiPangan Merauke.`
  return `https://wa.me/${digits}?text=${encodeURIComponent(text)}`
}
