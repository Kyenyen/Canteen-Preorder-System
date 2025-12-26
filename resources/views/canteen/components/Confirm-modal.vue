<template>
  <div
    v-show="visible"
    class="fixed inset-0 bg-black bg-opacity-50 z-[70] flex items-center justify-center p-4 fade-in backdrop-blur-sm"
  >
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 transform transition-all scale-100">
      <div class="text-center mb-6">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
          <i v-if="!isLoading" :class="iconClass" class="text-xl"></i>
          <div v-else class="animate-spin">
            <i class="fas fa-spinner text-xl text-red-600 dark:text-red-400"></i>
          </div>
        </div>
        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">{{ title }}</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ message }}</p>
      </div>
      <div class="flex gap-3">
        <button
          @click="cancel"
          :disabled="isLoading"
          class="w-full bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold py-3 rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Cancel
        </button>
        <button
          @click="confirm"
          :disabled="isLoading"
          class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl shadow-lg transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
        >
          <i v-if="isLoading" class="fas fa-spinner animate-spin"></i>
          {{ isLoading ? 'Processing...' : 'Confirm' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Props
const props = defineProps({
  isLoading: {
    type: Boolean,
    default: false
  }
})

// Reactive state
const visible = ref(false)
const title = ref('Confirm Action')
const message = ref('Are you sure you want to proceed?')
const iconClass = ref('fas fa-exclamation-triangle text-red-600 dark:text-red-400')

// Emit events
const emit = defineEmits(['confirm', 'cancel'])

// Open Modal
function openModal({ customTitle, customMessage, customIcon }) {
  if (customTitle) title.value = customTitle
  if (customMessage) message.value = customMessage
  if (customIcon) iconClass.value = customIcon
  visible.value = true
}

// Close Modal
function closeModal() {
  visible.value = false
}

// Cancel
function cancel() {
  if (!props.isLoading) {
    closeModal()
    emit('cancel')
  }
}

// Confirm
function confirm() {
  emit('confirm')
}

defineExpose({ openModal, closeModal })
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}
@keyframes fadeIn {
  from { opacity: 0 }
  to { opacity: 1 }
}
</style>
