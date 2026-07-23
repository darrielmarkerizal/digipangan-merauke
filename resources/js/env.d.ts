declare module '*.vue' {
  import type { DefineComponent } from 'vue'
  const component: DefineComponent<Record<string, unknown>, Record<string, unknown>, unknown>
  export default component
}

declare module '@inertiajs/core' {
  interface PageProps {
    auth: {
      user: {
        id: number
        name: string
        email: string
        permissions?: string[]
      } | null
    }
    flash: {
      success: string | null
      error: string | null
    }
  }
}

export {}
