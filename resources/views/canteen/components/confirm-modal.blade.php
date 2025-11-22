<!-- CONFIRMATION MODAL (Multi-purpose) -->
<div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[70] hidden items-center justify-center p-4 fade-in backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-sm w-full p-6 transform transition-all scale-100">
        <div class="text-center mb-6">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                <i id="confirm-icon" class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
            </div>
            <h3 id="confirm-title" class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Confirm Action</h3>
            <p id="confirm-message" class="text-sm text-gray-500 dark:text-gray-400 mt-2">Are you sure you want to proceed?</p>
        </div>
        <div class="flex gap-3">
            <button onclick="closeConfirmModal()" class="w-full bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold py-3 rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                Cancel
            </button>
            <button onclick="proceedConfirm()" id="confirm-proceed-btn" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl shadow-lg transition">
                Confirm
            </button>
        </div>
    </div>
</div>
