<template>
  <div class="min-h-screen w-full flex flex-col fade-in p-6 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">

    <div class="max-w-6xl mx-auto w-full mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Category Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Add, edit, or remove product categories (Admin Only).</p>
      </div>
    </div>

    
    <!-- Notification Display -->
    <div v-if="notification.message" 
        class="max-w-6xl mx-auto w-full mb-4 p-4 border-l-4 rounded-lg shadow-md transition-all duration-300"
        :class="notification.type === 'success' ? 'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300' : 'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300'">
      <p class="font-bold">{{ notification.type.charAt(0).toUpperCase() + notification.type.slice(1) }}:</p>
      <p>{{ notification.message }}</p>
    </div>

    <div class="max-w-6xl mx-auto w-full grid grid-cols-1 lg:grid-cols-3 gap-8">

      <div class="lg:col-span-1 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 h-fit sticky top-4">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-white mb-4">
          {{ isEditing ? 'Edit Category' : 'Create New Category' }}
        </h2>
        <form @submit.prevent="saveCategory" class="space-y-4">
          <input type="hidden" v-model="form.id">

          <div class="mb-4">
            <label for="name" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Category Name</label>
            <input type="text" v-model="form.name" id="name" required maxlength="100"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none"
              placeholder="e.g., Desserts">
          </div>

          <div class="flex space-x-2">
            <button type="submit" :disabled="loading"
              class="flex-1 px-4 py-3 bg-orange-600 text-white font-semibold rounded-xl shadow-lg hover:bg-orange-700 transition transform active:scale-95 disabled:opacity-50 flex items-center justify-center gap-2">
              <i v-if="loading" class="fas fa-spinner fa-spin"></i>
              {{ isEditing ? 'Save Changes' : 'Create Category' }}
            </button>
            <button v-if="isEditing" type="button" @click="resetForm()"
              class="px-4 py-3 text-gray-600 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 font-semibold rounded-xl shadow-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition transform active:scale-95">
              Cancel Edit
            </button>
          </div>
        </form>
      </div>

      <div class="lg:col-span-2 p-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-x-auto">
        <h2 class="text-2xl font-semibold text-gray-700 dark:text-white mb-4">Category List</h2>
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 font-medium border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th class="px-6 py-4">ID</th>
              <th class="px-6 py-4">Name</th>
              <th class="px-6 py-4">Product Count</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
            <tr v-if="loading && categories.length === 0">
              <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">Loading categories...</td>
            </tr>
            <tr v-else-if="categories.length === 0">
              <td colspan="4" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No categories found.</td>
            </tr>
            <tr v-for="category in categories" :key="category.category_id" class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
              <td class="px-6 py-4">{{ category.category_id }}</td>
              <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ category.name }}</td>
              <td class="px-6 py-4">{{ category.quantity || 0 }}</td> 
              <td class="px-6 py-4 text-right flex justify-end gap-2">
                <button @click="editCategory(category)" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteCategory(category.category_id, category.name)" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Delete">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Confirmation Modal (Replaces prohibited confirm()) -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-sm w-full p-6 transition-all transform scale-100">
        <h3 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">Confirm Deletion</h3>
        <p class="text-gray-700 dark:text-gray-300 mb-6">
          Are you sure you want to delete the category: 
          <span class="font-semibold italic">{{ categoryToDelete.name }}</span>? 
          This action cannot be undone and will fail if products are linked.
        </p>
        <div class="flex justify-end space-x-3">
          <button @click="showDeleteModal = false" :disabled="loading"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition disabled:opacity-50">
            Cancel
          </button>
          <button @click="confirmDelete" :disabled="loading"
            class="px-4 py-2 bg-red-600 text-white font-semibold rounded-xl shadow-md hover:bg-red-700 transition disabled:opacity-50 flex items-center gap-2">
            <i v-if="loading" class="fas fa-spinner fa-spin"></i>
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
import '@fortawesome/fontawesome-free/css/all.css' // Import Font Awesome

const API_URL = '/api/categories' // <-- REVERTED to /api/categories

// State
const categories = ref([])
const isEditing = ref(false)
const loading = ref(false)
const showDeleteModal = ref(false) // NEW: State for modal visibility
const categoryToDelete = reactive({ id: null, name: '' }) // NEW: State for item to delete
const notification = reactive({
    message: null,
    type: 'success'
})

const form = reactive({
    id: null,
    name: ''
})

// --- Methods ---

/**
 * Displays a notification message.
 */
const showNotification = (message, type = 'success') => {
    notification.message = message
    notification.type = type
    setTimeout(() => {
        notification.message = null
    }, 5000)
}

/**
 * Resets the form to 'Create' mode.
 */
const resetForm = () => {
    form.id = null
    form.name = ''
    isEditing.value = false
    loading.value = false
}

/**
 * Fetches all categories from the API.
 */
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

/**
 * Loads category data into the form for editing.
 */
const editCategory = (category) => {
    isEditing.value = true
    form.id = category.category_id
    form.name = category.name
}

/**
 * Handles form submission for both creating and updating a category.
 */
const saveCategory = async () => {
    loading.value = true
    
    // Construct payload
    const data = { name: form.name }

    try {
        let res;
        if (isEditing.value) {
            // Update
            res = await axios.put(`${API_URL}/${form.id}`, data)
        } else {
            // Create
            res = await axios.post(API_URL, data)
        }

        showNotification(res.data.message || (isEditing.value ? 'Category updated successfully.' : 'Category created successfully.'), 'success')
        resetForm()
        await fetchCategories()
    } catch (err) {
        console.error('Failed to save category:', err.response?.data || err)
        const errorMsg = err.response?.data?.message || 'An unknown error occurred during save.'
        showNotification(errorMsg, 'error')
    } finally {
        loading.value = false
    }
}

/**
 * Opens the delete confirmation modal. (REPLACED confirm())
 */
const deleteCategory = (id, name) => {
    categoryToDelete.id = id;
    categoryToDelete.name = name;
    showDeleteModal.value = true;
}


/**
 * Sends a DELETE request after modal confirmation. (NEW)
 */
const confirmDelete = async () => {
    const id = categoryToDelete.id;
    
    loading.value = true
    showDeleteModal.value = false // Close modal immediately
    try {
        const res = await axios.delete(`${API_URL}/${id}`)
        showNotification(res.data.message || 'Category deleted successfully.', 'success')
        await fetchCategories()
    } catch (err) {
        console.error('Failed to delete category:', err.response?.data || err)
        const errorMsg = err.response?.data?.message || 'Deletion failed. Check console for details.'
        showNotification(errorMsg, 'error')
    } finally {
        loading.value = false
        categoryToDelete.id = null;
        categoryToDelete.name = '';
    }
}

// Initial Data Fetch
onMounted(() => {
    fetchCategories()
})
</script>

<style scoped>
.fade-in { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
</style>