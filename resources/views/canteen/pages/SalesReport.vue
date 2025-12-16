<template>
  <div class="min-h-screen w-full flex flex-col fade-in p-6 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    
    <!-- Header -->
    <div class="max-w-7xl mx-auto w-full mb-8">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
          <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Sales Report</h1>
          <p class="text-gray-500 dark:text-gray-400 text-sm">View revenue, orders, and performance analytics.</p>
        </div>
        <button @click="refreshReport" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg shadow-md transition flex items-center gap-2" :disabled="loading">
          <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-sync'"></i>
          Refresh
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="max-w-7xl mx-auto w-full flex items-center justify-center py-20">
      <div class="text-center">
        <i class="fas fa-spinner fa-spin text-4xl text-orange-600 mb-4"></i>
        <p class="text-gray-600 dark:text-gray-400">Loading report data...</p>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else class="max-w-7xl mx-auto w-full space-y-6">
      
      <!-- Revenue Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Total Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Total Revenue</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">RM {{ formatCurrency(revenueStats.total_revenue) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-money-bill-wave text-green-600 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Average Order Value -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Avg Order Value</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">RM {{ formatCurrency(revenueStats.average_order_value) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-chart-line text-blue-600 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Today</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">RM {{ formatCurrency(revenueStats.today_revenue) }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-sun text-purple-600 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- This Week Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">This Week</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">RM {{ formatCurrency(revenueStats.this_week_revenue) }}</p>
            </div>
            <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-calendar-week text-cyan-600 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- This Month Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">This Month</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">RM {{ formatCurrency(revenueStats.this_month_revenue) }}</p>
            </div>
            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-calendar text-indigo-600 text-xl"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Order Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Total Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Total Orders</p>
              <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">{{ orderStats.total_orders }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-shopping-cart text-orange-600 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Completed Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Completed</p>
              <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-2">{{ orderStats.completed_orders }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Pending</p>
              <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400 mt-2">{{ orderStats.pending_orders }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
            </div>
          </div>
        </div>

        <!-- Cancelled Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-gray-500 dark:text-gray-400 text-sm font-semibold">Cancelled</p>
              <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-2">{{ orderStats.cancelled_orders }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
              <i class="fas fa-times-circle text-red-600 text-xl"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts and Top Selling Items Section -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Daily Charts (2 rows) -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Daily Revenue Chart -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Daily Revenue (Last 30 Days)</h3>
            <div class="h-80">
              <canvas ref="dailyRevenueChart"></canvas>
            </div>
          </div>

          <!-- Daily Orders Chart -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Daily Orders (Last 30 Days)</h3>
            <div class="h-80">
              <canvas ref="dailyOrdersChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Right Column: Revenue by Category and Top Selling Items -->
        <div class="space-y-6">
          <!-- Revenue by Category Chart -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Revenue by Category</h3>
            <div class="h-96">
              <canvas ref="categoryRevenueChart"></canvas>
            </div>
          </div>

          <!-- Top Selling Items Table (Compact) -->
          <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Selling Items</h3>
            </div>
            <div class="overflow-y-auto max-h-96">
              <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 font-medium border-b border-gray-200 dark:border-gray-700 sticky top-0">
                  <tr>
                    <th class="px-3 py-3">Product</th>
                    <th class="px-3 py-3 text-right">Qty</th>
                    <th class="px-3 py-3 text-right">Revenue</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                  <tr v-if="topSellingItems.length === 0">
                    <td colspan="3" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400 text-xs">No sales data</td>
                  </tr>
                  <tr v-for="(item, index) in topSellingItems.slice(0, 8)" :key="item.product_id" class="hover:bg-gray-50 dark:hover:bg-gray-750 transition">
                    <td class="px-3 py-3">
                      <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-600 overflow-hidden flex items-center justify-center border border-gray-200 dark:border-gray-600 flex-shrink-0">
                          <img v-if="item.photo" :src="getPhotoUrl(item.photo)" class="w-full h-full object-cover" :alt="item.name">
                          <i v-else class="fas fa-utensils text-gray-400 text-xs"></i>
                        </div>
                        <div class="truncate">
                          <p class="font-semibold text-gray-900 dark:text-white text-xs truncate">{{ index + 1 }}. {{ item.name }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-3 py-3 text-right font-semibold text-xs">{{ item.total_quantity }}</td>
                    <td class="px-3 py-3 text-right font-bold text-green-600 dark:text-green-400 text-xs">RM {{ formatCurrency(item.total_revenue) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import Chart from 'chart.js/auto'
import '@fortawesome/fontawesome-free/css/all.css'

// State
const loading = ref(false)
const revenueStats = reactive({
  total_revenue: 0,
  average_order_value: 0,
  today_revenue: 0,
  this_week_revenue: 0,
  this_month_revenue: 0,
})

const orderStats = reactive({
  total_orders: 0,
  completed_orders: 0,
  pending_orders: 0,
  cancelled_orders: 0,
})

const dailyRevenue = ref([])
const dailyOrders = ref([])
const revenueByCategory = ref([])
const topSellingItems = ref([])

// Chart references
const dailyRevenueChart = ref(null)
const dailyOrdersChart = ref(null)
const categoryRevenueChart = ref(null)
let dailyChart = null
let ordersChart = null
let categoryChart = null

// Methods
const formatCurrency = (value) => {
  return Number(value).toFixed(2)
}

const getPhotoUrl = (path) => {
  if (!path) return null
  if (path.startsWith('http')) return path
  return `/${path}`
}

const fetchSalesReport = async () => {
  loading.value = true
  try {
    const res = await axios.get('/api/admin/sales-report')
    const data = res.data

    // Update stats
    Object.assign(revenueStats, data.revenue_stats)
    Object.assign(orderStats, data.order_stats)
    dailyRevenue.value = data.daily_revenue
    dailyOrders.value = data.daily_orders
    revenueByCategory.value = data.revenue_by_category
    topSellingItems.value = data.top_selling_items

    // Initialize charts after data is loaded
    setTimeout(() => {
      initializeDailyRevenueChart()
      initializeDailyOrdersChart()
      initializeCategoryRevenueChart()
    }, 100)
  } catch (err) {
    console.error('Error fetching sales report:', err)
  } finally {
    loading.value = false
  }
}

const initializeDailyRevenueChart = () => {
  if (!dailyRevenueChart.value) return

  const ctx = dailyRevenueChart.value.getContext('2d')
  
  // Destroy previous chart if exists
  if (dailyChart) {
    dailyChart.destroy()
  }

  dailyChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: dailyRevenue.value.map(item => {
        const date = new Date(item.date)
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
      }),
      datasets: [
        {
          label: 'Daily Revenue (RM)',
          data: dailyRevenue.value.map(item => item.revenue),
          borderColor: '#ea580c',
          backgroundColor: 'rgba(234, 88, 12, 0.1)',
          tension: 0.4,
          fill: true,
          pointBackgroundColor: '#ea580c',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 5,
          pointHoverRadius: 7,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          labels: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
            callback: (value) => 'RM ' + value.toFixed(0),
          },
          grid: {
            color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.1)' : 'rgba(0, 0, 0, 0.1)',
          },
        },
        x: {
          ticks: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
          },
          grid: {
            display: false,
          },
        },
      },
    },
  })
}

const initializeDailyOrdersChart = () => {
  if (!dailyOrdersChart.value) return

  const ctx = dailyOrdersChart.value.getContext('2d')
  
  // Destroy previous chart if exists
  if (ordersChart) {
    ordersChart.destroy()
  }

  ordersChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: dailyOrders.value.map(item => {
        const date = new Date(item.date)
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
      }),
      datasets: [
        {
          label: 'Daily Orders',
          data: dailyOrders.value.map(item => item.order_count),
          backgroundColor: '#3b82f6',
          borderColor: '#2563eb',
          borderWidth: 1,
          borderRadius: 4,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          labels: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
          },
        },
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
            stepSize: 1,
          },
          grid: {
            color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.1)' : 'rgba(0, 0, 0, 0.1)',
          },
        },
        x: {
          ticks: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
          },
          grid: {
            display: false,
          },
        },
      },
    },
  })
}

const initializeCategoryRevenueChart = () => {
  if (!categoryRevenueChart.value) return

  const ctx = categoryRevenueChart.value.getContext('2d')
  
  // Destroy previous chart if exists
  if (categoryChart) {
    categoryChart.destroy()
  }

  const colors = ['#ea580c', '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#6366f1']

  categoryChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: revenueByCategory.value.map(item => item.category),
      datasets: [
        {
          label: 'Revenue by Category (RM)',
          data: revenueByCategory.value.map(item => item.revenue),
          backgroundColor: revenueByCategory.value.map((_, index) => colors[index % colors.length]),
          borderColor: revenueByCategory.value.map((_, index) => colors[index % colors.length]),
          borderWidth: 1,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y',
      plugins: {
        legend: {
          display: true,
          labels: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
          },
        },
      },
      scales: {
        x: {
          beginAtZero: true,
          ticks: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
            callback: (value) => 'RM ' + value.toFixed(0),
          },
          grid: {
            color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.1)' : 'rgba(0, 0, 0, 0.1)',
          },
        },
        y: {
          ticks: {
            color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280',
          },
          grid: {
            display: false,
          },
        },
      },
    },
  })
}

const refreshReport = () => {
  fetchSalesReport()
}

onMounted(() => {
  fetchSalesReport()
})
</script>

<style scoped>
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}
</style>
