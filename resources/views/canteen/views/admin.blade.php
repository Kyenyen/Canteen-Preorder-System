<!-- VIEW: ADMIN (KITCHEN) -->
<div id="view-admin" class="hidden h-full flex-col fade-in p-6 overflow-y-auto bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <div class="bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm text-orange-600 dark:text-orange-400">
                <i class="fas fa-fire-burner text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Kitchen Dashboard</h2>
                <p class="text-gray-500 dark:text-gray-400 text-sm">Manage incoming orders</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 px-4 py-2 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <span class="text-gray-500 dark:text-gray-400 text-xs uppercase font-bold">Pending</span>
            <p class="text-xl font-bold text-orange-600 dark:text-orange-400" id="pending-count">0</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-500 dark:text-gray-300 font-medium border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Student</th>
                    <th class="px-6 py-4">Items</th>
                    <th class="px-6 py-4">Pickup</th>
                    <th class="px-6 py-4">Total</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody id="orders-table-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                <!-- Orders injected by JS -->
            </tbody>
        </table>
        <div id="no-orders-msg" class="text-center py-10 text-gray-400 dark:text-gray-500 hidden">
            No orders found.
        </div>
    </div>
</div>
