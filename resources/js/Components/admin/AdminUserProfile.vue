<script setup lang="ts">
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { LogOut, Globe, Settings } from '@lucide/vue'
import { onClickOutside } from '@vueuse/core'
import { Icon } from '@/Components/ui'
import { useAuthGuard } from '@/Composables/useAuthGuard'

const { user, logout, isStrictSuperAdmin, isDistrictAdmin, userRegion } = useAuthGuard()

const isOpen = ref(false)
const popoverRef = ref<HTMLElement | null>(null)

onClickOutside(popoverRef, () => {
  isOpen.value = false
})

const handleLogout = () => {
  isOpen.value = false
  logout()
}

const roleBadgeLabel = computed(() => {
  if (isDistrictAdmin.value && userRegion.value) {
    return `Admin ${userRegion.value.name}`
  }
  if (isStrictSuperAdmin.value) {
    return 'Super Admin'
  }
  return 'Administrator'
})

const initials = computed(() => {
  if (!user.value?.name) return 'SA'
  const parts = user.value.name.trim().split(' ')
  if (parts.length >= 2) {
    return (parts[0][0] + parts[1][0]).toUpperCase()
  }
  return user.value.name.slice(0, 2).toUpperCase()
})
</script>

<template>
  <div ref="popoverRef" class="relative" @keydown.escape="isOpen = false">
    <button
      type="button"
      class="flex size-9 items-center justify-center rounded-full border border-border/80 bg-brand/10 font-bold text-brand text-xs uppercase shadow-xs transition-all duration-200 hover:scale-105 hover:bg-brand/15 hover:ring-2 hover:ring-brand/30 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand cursor-pointer"
      :class="{ 'ring-2 ring-brand/40 bg-brand/15': isOpen }"
      aria-label="Menu pengguna"
      :aria-expanded="isOpen"
      @click="isOpen = !isOpen"
    >
      {{ initials }}
    </button>

    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="transform scale-95 opacity-0 translate-y-1"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2.5 w-64 origin-top-right rounded-2xl border border-border/80 bg-white p-2 shadow-xl ring-1 ring-black/5 focus:outline-none z-50"
      >
        <div class="flex items-center gap-3 rounded-xl bg-muted/40 p-3">
          <div
            class="flex size-10 shrink-0 items-center justify-center rounded-full bg-brand font-bold text-white text-sm shadow-sm"
          >
            {{ initials }}
          </div>
          <div class="min-w-0 flex-1">
            <p class="truncate text-xs font-extrabold text-fg">
              {{ user ? user.name : 'Super Admin' }}
            </p>
            <p class="truncate text-[11px] text-fg-muted mt-0.5">
              {{ user ? user.email : 'admin@digipangan.id' }}
            </p>
            <span
              class="mt-1.5 inline-flex items-center rounded-full bg-brand-weak px-2 py-0.5 text-[10px] font-bold text-brand uppercase tracking-wider"
            >
              {{ roleBadgeLabel }}
            </span>
          </div>
        </div>

        <div class="my-1.5 h-px bg-border/80" />

        <div class="space-y-0.5">
          <Link
            href="/"
            class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-medium text-fg-muted transition-colors hover:bg-muted/60 hover:text-fg"
            @click="isOpen = false"
          >
            <Icon :icon="Globe" :size="15" class="text-fg-muted/80" />
            <span>Lihat Situs Publik</span>
          </Link>
          <Link
            v-if="isStrictSuperAdmin"
            href="/admin/pengaturan"
            class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-medium text-fg-muted transition-colors hover:bg-muted/60 hover:text-fg"
            @click="isOpen = false"
          >
            <Icon :icon="Settings" :size="15" class="text-fg-muted/80" />
            <span>Pengaturan Sistem</span>
          </Link>
        </div>

        <div class="my-1.5 h-px bg-border/80" />

        <button
          type="button"
          class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2 text-xs font-semibold text-danger transition-colors hover:bg-danger-weak cursor-pointer"
          @click="handleLogout"
        >
          <Icon :icon="LogOut" :size="15" />
          <span>Keluar dari Portal</span>
        </button>
      </div>
    </Transition>
  </div>
</template>
