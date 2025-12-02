<template>
  <div class="min-h-screen w-full flex flex-col fade-in p-6 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Header -->
    <div class="max-w-6xl mx-auto w-full mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Menu Management</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Add, edit, or remove items from the canteen menu.</p>
      </div>
      <button @click="openModal()" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl shadow-lg transition transform active:scale-95 flex items-center gap-2 font-bold" :disabled="loadingCategories">
        <i class="fas fa-plus"></i> Add New Item
      </button>
    </div>

    <!-- Global Notification (Success/General info only) -->
    <div v-if="notification.message" 
        class="max-w-6xl mx-auto w-full mb-4 p-4 border-l-4 rounded-lg shadow-md transition-all duration-300"
        :class="notification.type === 'success' ? 'bg-green-100 border-green-400 text-green-700 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300' : 'bg-red-100 border-red-400 text-red-700 dark:bg-red-900/30 dark:border-red-700 dark:text-red-300'">
      <p class="font-bold">{{ notification.type.charAt(0).toUpperCase() + notification.type.slice(1) }}:</p>
      <p>{{ notification.message }}</p>
    </div>

    <!-- Products Table -->
    <div class="max-w-6xl mx-auto w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 font-medium border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th class="px-6 py-4">Image</th>
              <th class="px-6 py-4">Name</th>
              <th class="px-6 py-4">Category</th>
              <th class="px-6 py-4">Price</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
            <tr v-if="loading">
                <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">Loading products...</td>
            </tr>
            <tr v-else-if="products.length === 0">
                <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">No products found.</td>
            </tr>
            <tr v-for="product in products" :key="product.product_id" class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
              
              <!-- Image -->
              <td class="px-6 py-4">
                <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-600 overflow-hidden flex items-center justify-center border border-gray-200 dark:border-gray-600">
                  <img v-if="product.photo" :src="getPhotoUrl(product.photo)" class="w-full h-full object-cover">
                  <i v-else class="fas fa-utensils text-gray-400"></i>
                </div>
              </td>
              
              <!-- Name & Desc -->
              <td class="px-6 py-4">
                <div class="font-bold text-gray-900 dark:text-white">{{ product.name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[200px]">{{ product.description }}</div>
              </td>
              
              <!-- Category (FIXED DISPLAY) -->
              <td class="px-6 py-4">
                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-md">
                    <!-- Check if category is object (from relationship) or string -->
                    {{ typeof product.category === 'object' && product.category ? product.category.name : product.category }}
                </span>
              </td>
              
              <!-- Price -->
              <td class="px-6 py-4 font-medium">RM {{ Number(product.price).toFixed(2) }}</td>
              
              <!-- Status -->
              <td class="px-6 py-4">
                <span v-if="product.is_available" class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 text-xs font-bold rounded-full">
                  Available
                </span>
                <span v-else class="px-2 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 text-xs font-bold rounded-full">
                  Sold Out
                </span>
              </td>
              
              <!-- Actions -->
              <td class="px-6 py-4 text-right flex justify-end gap-2">
                <button @click="openModal(product)" class="p-2 text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition" title="Edit">
                  <i class="fas fa-edit"></i>
                </button>
                <button @click="deleteProduct(product.product_id, product.name)" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition" title="Delete">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Product Modal (Add/Edit) -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4 fade-in backdrop-blur-sm">
      <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl shadow-2xl p-6 relative max-h-[90vh] overflow-y-auto">
        
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">
          {{ isEditing ? 'Edit Product' : 'Add New Product' }}
        </h3>

        <!-- NEW: Modal Error Alert (Displayed here instead of above table) -->
        <div v-if="modalError" class="mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-start gap-2 whitespace-pre-line text-left">
            <i class="fas fa-exclamation-circle mt-0.5"></i>
            <span>{{ modalError }}</span>
        </div>

        <!-- Form -->
        <form @submit.prevent="saveProduct" class="space-y-4">
          
          <!-- Image Upload Preview -->
          <div class="flex justify-center mb-4">
            <div class="relative w-32 h-32 bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-500 hover:border-orange-500 transition cursor-pointer group" @click="triggerFileInput">
                <img v-if="previewUrl" :src="previewUrl" class="w-full h-full object-cover">
                <div v-else class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                    <i class="fas fa-camera text-2xl mb-1"></i>
                    <span class="text-xs">Upload Photo</span>
                </div>
                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-edit text-white"></i>
                </div>
            </div>
            <input type="file" ref="fileInput" @change="handleFileChange" class="hidden" accept="image/*">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Name</label>
                <input type="text" v-model="form.name" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Price (RM)</label>
                <input type="number" step="0.01" v-model="form.price" required class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Category</label>
            <select v-model="form.category_id" required
              class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none">
              <option v-if="loadingCategories" disabled value="">Loading categories...</option>
              <option v-else-if="categories.length === 0" disabled value="">No categories available</option>
              <option v-for="cat in categories" :key="cat.category_id" :value="cat.category_id">
                  {{ cat.name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Description</label>
            <textarea v-model="form.description" rows="3" class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-orange-500 outline-none"></textarea>
          </div>

          <!-- Availability Toggle -->
          <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">Is Available?</span>
            <label class="relative inline-flex items-center cursor-pointer ml-auto">
              <input type="checkbox" v-model="form.is_available" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
            </label>
          </div>

          <div class="flex justify-end gap-3 mt-6">
            <button type="button" @click="closeModal" class="px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">Cancel</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow-md transition disabled:opacity-50">
                <i v-if="loading" class="fas fa-spinner fa-spin mr-2"></i>
                {{ isEditing ? 'Update Item' : 'Create Item' }}
            </button>
          </div>
        </form>

      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-70 flex items-center justify-center z-50 p-4">
      <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-sm w-full p-6 transition-all transform scale-100">
        <h3 class="text-xl font-bold text-red-600 dark:text-red-400 mb-4">Confirm Deletion</h3>
        <p class="text-gray-700 dark:text-gray-300 mb-6">
          Are you sure you want to delete the product: 
          <span class="font-semibold italic">{{ productToDelete.name }}</span>? 
          This action cannot be undone.
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
import '@fortawesome/fontawesome-free/css/all.css' 

// State
const products = ref([])
const categories = ref([]) 
const showModal = ref(false)
const isEditing = ref(false)
const loading = ref(false)
const loadingCategories = ref(false) 
const fileInput = ref(null)
const previewUrl = ref(null)
const selectedFile = ref(null)

const showDeleteModal = ref(false) 
const productToDelete = reactive({ id: null, name: '' }) 
const notification = reactive({ message: null, type: 'success' })
const modalError = ref('') // NEW: Tracks errors specific to the modal form

const form = reactive({
    id: null,
    name: '',
    price: '',
    description: '',
    category_id: '', 
    is_available: true
})

// --- Methods ---

const showNotification = (message, type = 'success') => {
    notification.message = message
    notification.type = type
    setTimeout(() => {
        notification.message = null
    }, 5000)
}

const fetchCategories = async () => {
    loadingCategories.value = true
    try {
        const res = await axios.get('/api/categories')
        categories.value = res.data
    } catch (err) {
        console.error('Error fetching categories:', err)
        showNotification('Failed to load categories. Cannot add/edit products.', 'error')
    } finally {
        loadingCategories.value = false
    }
}

const fetchProducts = async () => {
    loading.value = true;
    try {
        const res = await axios.get('/api/menu?all=true') 
        products.value = res.data
    } catch (err) {
        console.error('Error fetching menu:', err)
        showNotification('Failed to load menu items.', 'error')
    } finally {
        loading.value = false;
    }
}

const openModal = (product = null) => {
    // Reset modal error on open
    modalError.value = '';
    
    const defaultCategoryId = categories.value.length > 0 ? categories.value[0].category_id : '';

    if (product) {
        isEditing.value = true
        form.id = product.product_id
        form.name = product.name
        form.price = product.price
        form.description = product.description
        
        // Handle category logic (object vs ID)
        if (typeof product.category === 'object' && product.category) {
             form.category_id = product.category.category_id;
        } else {
             // Fallback if it's a string name or ID
             const matchingCategory = categories.value.find(c => c.name === product.category || c.category_id === product.category);
             form.category_id = matchingCategory ? matchingCategory.category_id : defaultCategoryId;
        }
        
        form.is_available = Boolean(product.is_available)
        previewUrl.value = getPhotoUrl(product.photo)
    } else {
        isEditing.value = false
        form.id = null
        form.name = ''
        form.price = ''
        form.description = ''
        form.category_id = defaultCategoryId 
        form.is_available = true
        previewUrl.value = null
    }
    selectedFile.value = null
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const getPhotoUrl = (path) => {
    if (!path) return null
    if (path.startsWith('http')) return path
    return `/${path}`
}

const triggerFileInput = () => fileInput.value.click()

const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        selectedFile.value = file
        previewUrl.value = URL.createObjectURL(file)
    }
}

const saveProduct = async () => {
    loading.value = true
    modalError.value = '' // Clear error state
    
    if (categories.value.length === 0 && !isEditing.value) {
         modalError.value = 'Cannot create product. Categories failed to load.';
         loading.value = false;
         return;
    }

    try {
        const data = new FormData()
        data.append('name', form.name)
        data.append('price', form.price)
        data.append('description', form.description || '')
        data.append('category_id', form.category_id) 
        data.append('is_available', form.is_available ? '1' : '0')
        
        if (selectedFile.value) {
            data.append('photo', selectedFile.value)
        }

        const config = { headers: { 'Content-Type': 'multipart/form-data' } }
        let res;

        if (isEditing.value) {
            data.append('_method', 'POST'); // Standard Laravel workaround for PUT files
            res = await axios.post(`/api/admin/products/${form.id}`, data, config)
        } else {
            res = await axios.post('/api/admin/products', data, config)
        }

        // Show Success
        showNotification(res.data.message || (isEditing.value ? 'Product updated.' : 'Product created.'), 'success')
        await fetchProducts() 
        closeModal()
    } catch (err) {
        console.error('Failed to save:', err.response?.data || err)
        
        // Extract validation messages
        if (err.response && err.response.data && err.response.data.errors) {
            modalError.value = Object.values(err.response.data.errors).flat().join('\n');
        } else if (err.response?.data?.message) {
            modalError.value = err.response.data.message;
        } else {
            modalError.value = 'Unknown error during product save.';
        }
    } finally {
        loading.value = false
    }
}

const deleteProduct = (id, name) => {
    productToDelete.id = id;
    productToDelete.name = name;
    showDeleteModal.value = true;
}

const confirmDelete = async () => {
    const id = productToDelete.id;
    loading.value = true
    showDeleteModal.value = false 
    try {
        const res = await axios.delete(`/api/admin/products/${id}`)
        showNotification(res.data.message || 'Product deleted successfully.', 'success')
        await fetchProducts() 
    } catch (err) {
        console.error('Failed to delete:', err.response?.data || err)
        const errorMsg = err.response?.data?.message || 'Deletion failed. Check console for details.'
        showNotification(errorMsg, 'error')
    } finally {
        loading.value = false
        productToDelete.id = null;
        productToDelete.name = '';
    }
}

onMounted(() => {
    fetchCategories()
    fetchProducts()
})
</script>

<style scoped>
.fade-in { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0 } to { opacity: 1 } }
</style>