<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import { Toaster } from 'vue-sonner'
import AdminSidebar from '@/Components/admin/AdminSidebar.vue'
import AdminTopbar from '@/Components/admin/AdminTopbar.vue'
import { useAuthGuard } from '@/Composables/useAuthGuard'
import { useFlashToast } from '@/Composables/useFlashToast'

defineProps<{
  title?: string
  subtitle?: string
}>()

const { requireAuth } = useAuthGuard()
useFlashToast()

onMounted(() => {
  requireAuth('/login')
})

const isSidebarOpen = ref(false)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = () => {
  isSidebarOpen.value = false
}
</script>

<template>
  <Head :title="title ? `${title} - Portal Admin DigiPangan` : 'Portal Admin - DigiPangan Merauke'" />
  <Toaster position="top-center" richColors />

  <div class="flex h-screen w-full overflow-hidden bg-bg text-fg">
    <div class="hidden lg:block shrink-0">
      <AdminSidebar />
    </div>

    <transition
      enter-active-class="transition-opacity duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isSidebarOpen"
        class="fixed inset-0 z-40 bg-fg/40 backdrop-blur-xs lg:hidden"
        @click="closeSidebar"
      />
    </transition>

    <transition
      enter-active-class="transition-transform duration-300 ease-out"
      enter-from-class="-translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transition-transform duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="-translate-x-full"
    >
      <div
        v-if="isSidebarOpen"
        class="fixed inset-y-0 left-0 z-50 w-64 shadow-xl lg:hidden"
      >
        <AdminSidebar @click="closeSidebar" />
      </div>
    </transition>

    <div class="flex flex-1 flex-col overflow-hidden">
      <AdminTopbar
        :title="title"
        :subtitle="subtitle"
        @toggle-sidebar="toggleSidebar"
      />

      <main class="flex-1 overflow-y-auto bg-bg py-4 md:py-6">
        <div class="w-full px-4 md:px-6 space-y-5">
          <header v-if="title || subtitle" class="flex flex-col justify-between gap-3 border-b border-border/80 pb-4 sm:flex-row sm:items-end">
            <div class="space-y-1">
              <p class="text-xs font-mono font-bold uppercase tracking-widest text-brand">
                Portal Administrasi
              </p>
              <h1 class="text-xl font-extrabold tracking-tight text-fg sm:text-2xl">
                {{ title }}
              </h1>
              <p v-if="subtitle" class="text-xs leading-relaxed text-fg-muted max-w-2xl">
                {{ subtitle }}
              </p>
            </div>

            <div class="flex shrink-0 items-center gap-3">
              <slot name="actions" />
            </div>
          </header>

          <slot />
        </div>
      </main>
    </div>
  </div>
</template>
