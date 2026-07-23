import { describe, it, expect } from 'vitest'
import { defineComponent, h, nextTick } from 'vue'
import { mount } from '@vue/test-utils'
import { axe } from '@/test/a11y'
import Accordion from './Accordion.vue'
import AccordionItem from './AccordionItem.vue'
import AlertDialog from './AlertDialog.vue'
import Button from './Button.vue'

describe('Accordion (Reka)', () => {
  const Harness = defineComponent({
    setup() {
      return () =>
        h(Accordion, null, {
          default: () => [
            h(AccordionItem, { value: 'a', title: 'Apa itu DigiPangan?' }, { default: () => 'Etalase komoditas lokal.' }),
            h(AccordionItem, { value: 'b', title: 'Bagaimana menghubungi petani?' }, { default: () => 'Lewat tombol WhatsApp.' }),
          ],
        })
    },
  })

  it('trigger memakai aria-expanded dan toggle saat diklik', async () => {
    const w = mount(Harness, { attachTo: document.body })
    const trigger = w.get('button')
    expect(trigger.attributes('aria-expanded')).toBe('false')
    await trigger.trigger('click')
    await nextTick()
    expect(trigger.attributes('aria-expanded')).toBe('true')
    w.unmount()
  })

  it('tanpa pelanggaran a11y', async () => {
    const w = mount(Harness, { attachTo: document.body })
    expect(await axe(document.body)).toHaveNoViolations()
    w.unmount()
  })
})

describe('AlertDialog (Reka)', () => {
  const Harness = defineComponent({
    setup(_, { emit }) {
      return () =>
        h(
          AlertDialog,
          {
            title: 'Hapus produk ini?',
            description: 'Tindakan ini tidak dapat dibatalkan.',
            onConfirm: () => emit('confirm'),
          },
          { trigger: () => h(Button, null, { default: () => 'Hapus' }) },
        )
    },
  })

  it('membuka dialog dengan role & judul saat trigger diklik', async () => {
    const w = mount(Harness, { attachTo: document.body })
    await w.get('button').trigger('click')
    await nextTick()
    const dialog = document.querySelector('[role="alertdialog"]')
    expect(dialog).not.toBeNull()
    expect(document.body.textContent).toContain('Hapus produk ini?')
    w.unmount()
  })

  it('tombol konfirmasi mengemit confirm', async () => {
    const w = mount(Harness, { attachTo: document.body })
    await w.get('button').trigger('click')
    await nextTick()
    const buttons = Array.from(document.querySelectorAll('button'))
    const confirm = buttons.find((b) => b.textContent?.trim() === 'Hapus' && b.closest('[role="alertdialog"]'))
    confirm?.dispatchEvent(new MouseEvent('click', { bubbles: true }))
    await nextTick()
    expect(w.emitted('confirm')).toBeTruthy()
    w.unmount()
  })

  it('tanpa pelanggaran a11y saat terbuka', async () => {
    const w = mount(Harness, { attachTo: document.body })
    await w.get('button').trigger('click')
    await nextTick()
    expect(await axe(document.body)).toHaveNoViolations()
    w.unmount()
  })
})
