<template>
  <template v-if="isStudent">
    
    <div
      v-show="isOpen"
      @click="toggleCart"
      class="fixed inset-0 bg-black bg-opacity-50 z-30 transition-opacity duration-300 backdrop-blur-sm"
    ></div>

    <div
      :class="['w-full md:w-[400px] bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col h-full shadow-2xl z-[110] fixed top-0 right-0 transform transition-transform duration-300', isOpen ? 'translate-x-0' : 'translate-x-full']"
    >
      <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800">
        <h3 class="font-extrabold text-2xl text-gray-800 dark:text-white">Shopping Cart</h3>
        <div class="flex items-center gap-3">
           <button
            v-if="false" 
            @click="toggleEditMode"
            class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-semibold"
          >
            <i class="fas fa-pen mr-1"></i>Edit
          </button>
          <button @click="toggleCart" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>
      </div>

      <div class="flex-1 overflow-y-auto p-6">
        <div v-if="cart.items.length" class="space-y-3">
          <div v-for="item in cart.items" :key="item.id" class="flex justify-between items-center bg-gray-50 dark:bg-gray-700 p-3 rounded-xl shadow-sm">
            <div>
              <p class="font-bold text-gray-800 dark:text-white">{{ item.name }}</p>
              <p class="text-sm text-gray-500 dark:text-gray-400">RM {{ item.price.toFixed(2) }} x {{ item.qty }}</p>
            </div>
            <div class="flex items-center gap-2">
              <button @click="updateQty(item.id, item.qty - 1)" class="text-orange-600 px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded">-</button>
              <span>{{ item.qty }}</span>
              <button @click="updateQty(item.id, item.qty + 1)" class="text-orange-600 px-2 py-1 bg-gray-200 dark:bg-gray-600 rounded">+</button>
              <button @click="removeItem(item.id)" class="text-red-500 ml-2"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        </div>
        <p v-else class="text-center text-gray-400 dark:text-gray-500 mt-6">Your cart is empty.</p>
      </div>

      <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 space-y-4">
        <div class="flex justify-between items-center">
          <span class="text-gray-600 dark:text-gray-400 font-medium">Total</span>
          <span class="text-2xl font-extrabold text-gray-900 dark:text-white">RM {{ cart.subtotal.toFixed(2) }}</span>
        </div>

        <button
          @click="goToCheckout"
          class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2"
          :disabled="cart.items.length === 0"
        >
          <i class="fas fa-shopping-bag"></i>
          <span>Checkout</span>
        </button>
      </div>
    </div>

    <button
      @click="toggleCart"
      :class="[
        'fixed right-6 bg-orange-600 hover:bg-orange-700 text-white w-16 h-16 rounded-full shadow-2xl flex items-center justify-center z-30 active:scale-90 hover:scale-105 dark:shadow-orange-900/50',
        'transition-all duration-300 ease-in-out cart-button'
      ]"
      :style="{ bottom: cartButtonBottom }"
    >
      <div class="relative">
        <i class="fas fa-basket-shopping text-2xl"></i>
        <span
          v-if="cart.totalItems > 0"
          class="absolute -top-3 -right-3 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-orange-600"
        >
          {{ cart.totalItems }}
        </span>
      </div>
    </button>
  </template>
</template>

<script setup>
import { ref, onMounted, inject, watch, computed } from 'vue' // Added computed
import { useCartStore } from '../../../js/stores/cart'
import { useAuthStore } from '../../../js/stores/auth' // 2. Import Auth Store
import { useRouter } from 'vue-router'

const cart = useCartStore()
const auth = useAuthStore() // Initialize auth
const router = useRouter()

const isOpen = ref(false)
const cartButtonBottom = ref('1.5rem') 

// 3. Create the Logic Check
const isStudent = computed(() => {
  return auth.user && auth.user.role === 'user'
})

// Inject notification state from parent
const notificationState = inject('notificationState', null)

if (notificationState) {
  watch(() => notificationState.value.show, (isNotificationVisible) => {
    if (isNotificationVisible) {
      cartButtonBottom.value = '120px'
    } else {
      cartButtonBottom.value = '1.5rem'
    }
  }, { immediate: true })
}

onMounted(() => {
  // 4. Optimization: Only fetch cart data if the user is actually a student
  if (isStudent.value) {
    cart.fetchCart()
  }
})

// Watch for login changes (e.g. if user logs in while on this page)
watch(isStudent, (newVal) => {
  if (newVal) {
    cart.fetchCart()
  }
})

const toggleCart = () => {
  isOpen.value = !isOpen.value
}

const toggleEditMode = () => {
  // Optional: handle edit mode
}

const removeItem = async (id) => {
  await cart.removeItem(id)
}

const updateQty = async (id, qty) => {
  if (qty < 1) return
  await cart.updateQuantity(id, qty)
}

const goToCheckout = () => {
  toggleCart()
  router.push('/checkout')
}

defineExpose({
  toggleCart
})
</script>