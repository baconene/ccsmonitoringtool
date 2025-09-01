// resources/js/composables/useToast.ts
import { ref } from "vue"

const message = ref<string>("")
const show = ref<boolean>(false)

export function useToast() {
  const showMessage = (msg: string) => {
    message.value = msg
    show.value = true
    setTimeout(() => {
      show.value = false
    }, 2000)
  }

  return {
    message,
    show,
    showMessage,
  }
}
