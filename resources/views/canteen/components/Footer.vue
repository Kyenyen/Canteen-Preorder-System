<template>
  <footer class="w-full bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-200 transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-center">
      <p class="text-sm text-center">
        © {{ currentYear }} KantinCanteen Preorder System. All rights reserved.
      </p>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'

const currentYear = ref(new Date().getFullYear())
</script>
