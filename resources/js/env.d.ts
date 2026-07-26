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
        phone: string | null
        avatar_url: string | null
      } | null
      roles: string[]
      permissions: string[]
    }
    flash: {
      success: string | null
      error: string | null
      info: string | null
    }
    app: {
      name: string
      env: string
      locale: string
      whatsapp_admin: string | null
      contact_email: string | null
    }
    query: Record<string, string>
  }
}

export {}
