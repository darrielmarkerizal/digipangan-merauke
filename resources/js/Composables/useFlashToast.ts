import { watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { toast } from 'vue-sonner'

export function useFlashToast() {
  const page = usePage()

  watch(
    () => page.props.flash,
    (flash: any) => {
      if (!flash) return

      if (flash.success) {
        toast.success(flash.success)
      }
      if (flash.error) {
        toast.error(flash.error)
      }
      if (flash.info) {
        toast.info(flash.info)
      }
    },
    { immediate: true, deep: true }
  )
}
