<template>
  <div class="min-h-screen w-full flex flex-col fade-in p-6 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    <!-- Header -->
    <div class="max-w-6xl mx-auto w-full mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Category Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Organize your menu into categories.</p>
      </div>
      
      <!-- Toggle Add Form (Mobile friendly) -->
      <button @click="showAddForm = !showAddForm" class="md:hidden px-4 py-2 bg-orange-600 text-white rounded-lg shadow-md font-bold">
        <i class="fas" :class="showAddForm ? 'fa-minus' : 'fa-plus'"></i> {{ showAddForm ? 'Hide Form' : 'New Category' }}
      </button>
    </div>

    <!-- Notification -->
    <div v-if="notification.message" 
        class="max-w-6xl mx-auto w-full mb-6 p-4 border-l-4 rounded-lg shadow-md transition-all duration-300 flex items-center gap-3"
        :class="notification.type === 'success' ? 'bg-green-50 border-green-500 text-green-700 dark:bg-green-900/20' : 'bg-red-50 border-red-500 text-red-700 dark:bg-red-900/20'">
      <i :class="notification.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
      <p class="font-medium">{{ notification.message }}</p>
    </div>

    <div class="max-w-6xl mx-auto w-full grid grid-cols-1 lg:grid-cols-3 gap-8">

      <!-- Left Panel: Create/Edit Form -->
      <div :class="['lg:col-span-1 p-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 h-fit sticky top-6 transition-all', showAddForm ? 'block' : 'hidden md:block']">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                <i :class="isEditing ? 'fas fa-edit text-blue-500' : 'fas fa-plus-circle text-orange-500'" class="mr-2"></i>
                {{ isEditing ? 'Edit Category' : 'New Category' }}
            </h2>
            <button v-if="isEditing" @click="resetForm" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i class="fas fa-times"></i> Cancel
            </button>
        </div>

        <form @submit.prevent="saveCategory" class="space-y-5">
          <input type="hidden" v-model="form.id">

          <div>
            <label for="name" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Category Name</label>
            <input type="text" v-model="form.name" id="name" required maxlength="50"
              class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none transition"
              placeholder="e.g. Western Food">
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ form.name?.length || 0 }}/50 characters</p>
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-3.5 bg-gray-900 dark:bg-orange-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
            <span>{{ isEditing ? 'Update Category' : 'Create Category' }}</span>
          </button>
        </form>
      </div>

      <!-- Right Panel: Category Grid -->
      <div class="lg:col-span-2">
        
        <!-- Loading State -->
        <div v-if="loading && categories.length === 0" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="n in 4" :key="n" class="h-32 bg-gray-200 dark:bg-gray-700 rounded-2xl animate-pulse"></div>
        </div>

        <!-- Empty State -->
        <div v-else-if="categories.length === 0" class="text-center py-12 bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700">
            <i class="fas fa-layer-group text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">No categories found. Create one to get started!</p>
        </div>

        <!-- Grid Cards (Visible on all screens now) -->
        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="category in categories" :key="category.category_id" 
                class="group bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-orange-200 dark:hover:border-orange-800 transition-all relative overflow-hidden">
                
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 dark:bg-gray-700 rounded-bl-full -mr-10 -mt-10 transition-colors group-hover:bg-orange-100 dark:group-hover:bg-gray-600"></div>

                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-2">
                        <span class="font-mono text-xs text-gray-400 dark:text-gray-500">#{{ category.category_id }}</span>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="editCategory(category)" class="w-8 h-8 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center hover:bg-blue-100 transition">
                                <i class="fas fa-pencil-alt text-xs"></i>
                            </button>
                            <button @click="deleteCategory(category.category_id, category.name)" class="w-8 h-8 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-100 transition">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-1">{{ category.name }}</h3>
                    
                    <!-- Product Count Badge -->
                    <div class="flex items-center gap-2 mt-4">
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-full text-xs font-bold flex items-center gap-2">
                            <i class="fas fa-box-open"></i>
                            <!-- FIX: Using 'quantity' from DB instead of 'products_count' -->
                            {{ category.quantity || 0 }} Products
                        </span>
                    </div>
                </div>
            </div>
        </div>
      </div>

    </div>

    <!-- Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-[70] p-4 backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 transform scale-100 transition-all">
        <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center text-red-600 dark:text-red-400 mb-4 mx-auto">
            <i class="fas fa-trash-alt text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-center text-gray-900 dark:text-white mb-2">Delete Category?</h3>
        <p class="text-center text-gray-500 dark:text-gray-400 mb-6 text-sm">
          Are you sure you want to delete <span class="font-bold text-gray-800 dark:text-white">"{{ categoryToDelete.name }}"</span>?
          <br>This action cannot be undone.
        </p>
        <div class="flex gap-3">
          <button @click="showDeleteModal = false" class="flex-1 px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
            Cancel
          </button>
          <button @click="confirmDelete" class="flex-1 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 dark:shadow-none">
            Delete
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import '@fortawesome/fontawesome-free/css/all.css'

const API_URL = '/api/categories'

// State
const categories = ref([])
const isEditing = ref(false)
const loading = ref(false)
const showAddForm = ref(false) // For mobile toggle
const showDeleteModal = ref(false)
const categoryToDelete = reactive({ id: null, name: '' })
const notification = reactive({ message: null, type: 'success' })

const form = reactive({
    id: null,
    name: ''
})

const showNotification = (message, type = 'success') => {
    notification.message = message
    notification.type = type
    setTimeout(() => notification.message = null, 4000)
}

const resetForm = () => {
    form.id = null
    form.name = ''
    isEditing.value = false
    loading.value = false
}

const fetchCategories = async () => {
    loading.value = true
    try {
        const res = await axios.get(API_URL)
        categories.value = res.data
    } catch (err) {
        console.error('Error fetching categories:', err)
        showNotification('Failed to load categories.', 'error')
    } finally {
        loading.value = false
    }
}

const editCategory = (category) => {
    isEditing.value = true
    form.id = category.category_id
    form.name = category.name
    // On mobile, scroll to top/show form
    showAddForm.value = true
    window.scrollTo({ top: 0, behavior: 'smooth' })
}

const saveCategory = async () => {
    loading.value = true
    const data = { name: form.name }

    try {
        let res;
        if (isEditing.value) {
            res = await axios.put(`${API_URL}/${form.id}`, data)
        } else {
            res = await axios.post(API_URL, data)
        }

        showNotification(res.data.message, 'success')
        resetForm()
        await fetchCategories()
    } catch (err) {
        console.error('Failed to save category:', err)
        const errorMsg = err.response?.data?.message || 'An error occurred during save.'
        showNotification(errorMsg, 'error')
    } finally {
        loading.value = false
    }
}

const deleteCategory = (id, name) => {
    categoryToDelete.id = id;
    categoryToDelete.name = name;
    showDeleteModal.value = true;
}

const confirmDelete = async () => {
    const id = categoryToDelete.id;
    loading.value = true
    showDeleteModal.value = false 
    try {
        const res = await axios.delete(`${API_URL}/${id}`)
        showNotification(res.data.message, 'success')
        await fetchCategories()
    } catch (err) {
        console.error('Failed to delete category:', err)
        const errorMsg = err.response?.data?.message || 'Deletion failed. Check console for details.'
        showNotification(errorMsg, 'error')
    } finally {
        loading.value = false
        categoryToDelete.id = null;
        categoryToDelete.name = '';
    }
}

onMounted(() => {
    fetchCategories()
})
</script>

<style scoped>
.fade-in { animation: fadeIn 0.3s ease-in-out; }
</style>