import { ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useDebounceFn } from '@vueuse/core'

export function useSearch(paramName = 'search', debounceMs = 300) {
  const page = usePage()

  const getInitialValue = () => {
    if (typeof window === 'undefined') return ''
    const urlParams = new URLSearchParams(window.location.search)
    if (urlParams.has(paramName)) {
      return urlParams.get(paramName) || ''
    }
    const pageFilters = (page.props as any)?.filters
    return (pageFilters && pageFilters[paramName]) || ''
  }

  const search = ref<string>(getInitialValue())

  const updateUrlQuery = useDebounceFn((queryVal: string) => {
    const trimmed = queryVal.trim()
    const currentUrl = new URL(window.location.href)
    const existingSearch = currentUrl.searchParams.get(paramName) || ''

    if (trimmed === existingSearch) return

    const queryData: Record<string, any> = {}

    currentUrl.searchParams.forEach((val, key) => {
      if (key !== paramName) {
        queryData[key] = val
      }
    })

    if (trimmed) {
      queryData[paramName] = trimmed
    }

    router.get(currentUrl.pathname, queryData, {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    })
  }, debounceMs)

  watch(search, (newVal) => {
    updateUrlQuery(newVal)
  })

  return {
    search,
  }
}
