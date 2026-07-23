import { cn as tvCn } from 'tailwind-variants'
import type { CnOptions } from 'tailwind-variants'

export function cn(...classes: CnOptions): string {
  return tvCn(...classes) ?? ''
}
