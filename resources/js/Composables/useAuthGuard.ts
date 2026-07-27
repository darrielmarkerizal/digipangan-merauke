import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'
import axios from 'axios'

export interface AuthUser {
  id: number | string
  name: string
  email: string
  phone?: string
  avatar_url?: string
}

export function useAuthGuard() {
  const page = usePage()

  const user = computed<AuthUser | null>(() => {
    return (page.props.auth as any)?.user || null
  })

  const roles = computed<string[]>(() => {
    return (page.props.auth as any)?.roles || []
  })

  const permissions = computed<string[]>(() => {
    return (page.props.auth as any)?.permissions || []
  })

  const isAuthenticated = computed(() => !!user.value)

  const isSuperAdmin = computed(() => {
    return roles.value.includes('super_admin') || roles.value.includes('admin')
  })

  const hasRole = (roleName: string): boolean => {
    return roles.value.includes(roleName)
  }

  const hasPermission = (permissionName: string): boolean => {
    return permissions.value.includes(permissionName)
  }

  const requireAuth = (redirectTo = '/login'): boolean => {
    if (!isAuthenticated.value) {
      toast.error('Akses ditolak', {
        description: 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.',
      })
      router.visit(redirectTo)
      return false
    }
    return true
  }

  const requireRole = (requiredRoles: string[], redirectTo = '/login'): boolean => {
    if (!requireAuth(redirectTo)) return false

    const hasAnyRole = requiredRoles.some((r) => roles.value.includes(r))
    if (!hasAnyRole) {
      toast.error('Akses tidak diizinkan', {
        description: 'Akun Anda tidak memiliki peran untuk mengakses modul ini.',
      })
      router.visit('/')
      return false
    }
    return true
  }

  const logout = async (): Promise<void> => {
    try {
      await axios.post('/api/v1/auth/logout')
    } catch {
    } finally {
      router.post('/logout')
    }
  }

  return {
    user,
    roles,
    permissions,
    isAuthenticated,
    isSuperAdmin,
    hasRole,
    hasPermission,
    requireAuth,
    requireRole,
    logout,
  }
}
