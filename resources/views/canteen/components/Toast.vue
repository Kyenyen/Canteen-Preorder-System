<template>
  <div 
    class="fixed bottom-6 right-6 bg-gray-800 dark:bg-gray-700 text-white px-6 py-3 rounded-lg shadow-xl transition-all duration-300 flex items-center gap-3 z-[100] border border-gray-700 dark:border-gray-600"
    :class="visible ? 'translate-y-0 opacity-100' : 'translate-y-40 opacity-0'"
  >
    <i class="fas fa-check-circle text-green-400"></i>
    <span class="font-medium">{{ message }}</span>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const visible = ref(false)
const message = ref('')
let timeoutId = null

const show = (msg = 'Operation successful') => {
  message.value = msg
  visible.value = true

  // Clear existing timer if showing a new toast immediately
  if (timeoutId) clearTimeout(timeoutId)

  // Auto hide after 3 seconds
  timeoutId = setTimeout(() => {
    visible.value = false
  }, 3000)
}

// Expose the show method to parent components
defineExpose({ show })
</script>