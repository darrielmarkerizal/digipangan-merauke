<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import {
  House,
  ShoppingBasket,
  MapPin,
  Users,
  Newspaper,
  Info,
  Sprout,
} from '@lucide/vue'
import Icon from '@/Components/ui/Icon.vue'
import { Toaster } from 'vue-sonner'
import { useFlashToast } from '@/Composables/useFlashToast'

const page = usePage()
useFlashToast()

const navItems = [
  { href: '/', label: 'Beranda', icon: House },
  { href: '/produk', label: 'Produk', icon: ShoppingBasket },
  { href: '/wilayah', label: 'Wilayah', icon: MapPin },
  { href: '/petani', label: 'Petani', icon: Users },
  { href: '/berita', label: 'Berita', icon: Newspaper },
  { href: '/tentang', label: 'Tentang', icon: Info },
]

const bottomNav = navItems.filter((i) => i.label !== 'Petani')

const isActive = (href: string) =>
  href === '/' ? page.url === '/' : page.url.startsWith(href)
</script>

<template>
  <Toaster position="top-center" richColors />
  <div class="flex min-h-[100dvh] flex-col">
    <header class="fixed inset-x-0 top-0 z-40 px-4 pt-3">
      <div
        class="mx-auto flex h-15 max-w-6xl items-center justify-between gap-4 rounded-full bg-card/70 pl-3 pr-3 shadow-[0_10px_40px_-12px_rgba(20,40,31,0.18)] ring-1 ring-fg/5 backdrop-blur-xl"
      >
        <div class="flex items-center gap-4">
          <Link href="/" class="flex items-center gap-2.5">
            <span
              class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand to-brand-strong text-on-brand shadow-sm shadow-brand/30"
            >
              <Icon :icon="Sprout" :size="18" />
            </span>
            <span class="text-lg font-bold tracking-tight text-fg">
              DigiPangan<span class="font-medium text-fg-muted"> Merauke</span>
            </span>
          </Link>
        </div>


        <nav class="hidden items-center gap-0.5 lg:flex">
          <Link
            v-for="item in navItems"
            :key="item.href"
            :href="item.href"
            class="rounded-full px-4 py-2 text-sm font-medium transition-colors duration-200"
            :class="
              isActive(item.href)
                ? 'bg-brand-weak text-brand'
                : 'text-fg-muted hover:bg-muted hover:text-fg'
            "
            :aria-current="isActive(item.href) ? 'page' : undefined"
          >
            {{ item.label }}
          </Link>
        </nav>
      </div>
    </header>

    <main class="flex-1 pt-16 pb-24 md:pt-20 md:pb-0">
      <slot />
    </main>

    <footer class="border-t border-border/80 bg-card/60">
      <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-12 lg:gap-12">
          <div class="space-y-4 lg:col-span-4">
            <span class="flex items-center gap-2.5">
              <span
                class="flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand to-brand-strong text-on-brand shadow-sm shadow-brand/20"
              >
                <Icon :icon="Sprout" :size="18" />
              </span>
              <span class="text-lg font-bold tracking-tight text-fg">
                DigiPangan<span class="font-medium text-fg-muted"> Merauke</span>
              </span>
            </span>
            <p class="text-sm leading-relaxed text-fg-muted">
              Etalase komoditas lokal dan pemetaan kawasan transmigrasi Merauke.
              Membuka potensi pangan masa depan Indonesia melalui teknologi digital.
            </p>
            <div
              class="flex items-center gap-2 pt-1 text-xs font-medium text-fg-muted"
            >
              <Icon :icon="MapPin" :size="15" class="text-brand" />
              <span>Kawasan Transmigrasi Merauke, Papua Selatan</span>
            </div>
          </div>

          <div class="space-y-3 lg:col-span-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-fg/80">
              Kolaborasi Strategis
            </h3>
            <div
              class="inline-flex flex-wrap items-center gap-6 rounded-2xl border border-border/80 bg-white px-6 py-4 shadow-sm"
            >
              <img
                src="/images/logos/logo-ugm-horizontal.png"
                alt="Universitas Gadjah Mada"
                class="h-10 sm:h-11 w-auto object-contain"
              />
              <div class="h-8 w-px bg-slate-200" />
              <img
                src="/images/logos/logotext-kementerian-transmigrasi.png"
                alt="Kementerian Transmigrasi RI"
                class="h-10 sm:h-11 w-auto object-contain"
              />
            </div>
            <p class="text-xs text-fg-muted">
              Sinergi Universitas Gadjah Mada &amp; Kementerian Transmigrasi RI
              untuk Kedaulatan Pangan Nusantara.
            </p>
          </div>

          <div class="space-y-3 lg:col-span-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-fg/80">
              Navigasi Cepat
            </h3>
            <nav class="grid grid-cols-2 gap-2.5 text-sm">
              <Link
                v-for="item in navItems"
                :key="item.href"
                :href="item.href"
                class="font-medium text-fg-muted transition-colors hover:text-brand"
              >
                {{ item.label }}
              </Link>
            </nav>
          </div>
        </div>

        <div
          class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-border/70 pt-6 text-center text-xs text-fg-muted sm:flex-row sm:text-left"
        >
          <p>
            &copy; 2026 Universitas Gadjah Mada &amp; Kementerian Transmigrasi RI. Hak Cipta Dilindungi.
          </p>
          <p class="font-medium">
            DigiPangan Merauke v1.0 &bull; Membangun Kedaulatan Pangan
          </p>
        </div>
      </div>
    </footer>

    <nav
      class="fixed inset-x-0 bottom-0 z-40 grid grid-cols-5 border-t border-border bg-card/85 pb-[env(safe-area-inset-bottom)] backdrop-blur-xl md:hidden"
      aria-label="Navigasi utama"
    >
      <Link
        v-for="item in bottomNav"
        :key="item.href"
        :href="item.href"
        class="flex min-h-14 flex-col items-center justify-center gap-0.5 text-xs font-medium transition-colors"
        :class="isActive(item.href) ? 'text-brand' : 'text-fg-muted'"
        :aria-current="isActive(item.href) ? 'page' : undefined"
      >
        <span
          class="flex size-9 items-center justify-center rounded-full transition-colors"
          :class="isActive(item.href) ? 'bg-brand-weak' : ''"
        >
          <Icon :icon="item.icon" :size="20" />
        </span>
        {{ item.label }}
      </Link>
    </nav>
  </div>
</template>
