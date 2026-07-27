import { router } from '@inertiajs/vue3'

export function useSort() {
  const getSortParam = (): string => {
    if (typeof window === 'undefined') return ''
    return new URLSearchParams(window.location.search).get('sort') || ''
  }

  const getSortDirection = (column: string): 'asc' | 'desc' | null => {
    const current = getSortParam()
    if (current === column) return 'asc'
    if (current === `-${column}`) return 'desc'
    return null
  }

  const sortBy = (column: string) => {
    if (typeof window === 'undefined') return
    const currentUrl = new URL(window.location.href)
    const params = new URLSearchParams(currentUrl.search)
    const currentSort = params.get('sort') || ''

    let newSort = column
    if (currentSort === column) {
      newSort = `-${column}`
    } else if (currentSort === `-${column}`) {
      newSort = ''
    }

    if (newSort) {
      params.set('sort', newSort)
    } else {
      params.delete('sort')
    }

    params.set('page', '1')

    router.get(currentUrl.pathname, Object.fromEntries(params.entries()), {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  }

  return {
    getSortParam,
    getSortDirection,
    sortBy,
  }
}
