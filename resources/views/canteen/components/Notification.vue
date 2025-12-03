<template>
  <Transition name="notification">
    <div
      v-if="visible"
      :class="[
        'fixed bottom-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden',
      ]"
    >
      <div class="p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <i
              :class="[
                'fas text-xl',
                type === 'success' ? 'fa-check-circle text-green-500' : '',
                type === 'error' ? 'fa-times-circle text-red-500' : '',
                type === 'warning' ? 'fa-exclamation-circle text-yellow-500' : '',
                type === 'info' ? 'fa-info-circle text-blue-500' : '',
              ]"
            ></i>
          </div>
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ title }}</p>
            <p v-if="message" class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ message }}</p>
          </div>
          <div class="ml-4 flex-shrink-0 flex">
            <button
              @click="close"
              class="bg-white dark:bg-gray-800 rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'success',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    required: true
  },
  message: {
    type: String,
    default: ''
  },
  duration: {
    type: Number,
    default: 3000
  }
})

const emit = defineEmits(['close'])

const visible = ref(false)
let timeout = null

watch(() => props.show, (newVal) => {
  if (newVal) {
    visible.value = true
    if (timeout) clearTimeout(timeout)
    if (props.duration > 0) {
      timeout = setTimeout(() => {
        close()
      }, props.duration)
    }
  }
})

const close = () => {
  visible.value = false
  setTimeout(() => {
    emit('close')
  }, 300)
}
</script>

<style scoped>
.notification-enter-active,
.notification-leave-active {
  transition: all 0.3s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%);
}
</style>
