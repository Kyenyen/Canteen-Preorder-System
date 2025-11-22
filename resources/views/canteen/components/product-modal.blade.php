<!-- NEW: Product Detail Modal -->
<div id="product-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden items-end sm:items-center justify-center fade-in backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 w-full sm:max-w-lg sm:rounded-2xl rounded-t-2xl shadow-2xl overflow-hidden transform transition-transform duration-300 scale-100">
        <!-- Close Button -->
        <button onclick="closeProductDetail()" class="absolute top-4 right-4 z-10 bg-white/80 dark:bg-black/50 backdrop-blur p-2 rounded-full text-gray-500 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white hover:bg-white dark:hover:bg-gray-700 transition">
            <i class="fas fa-times text-xl"></i>
        </button>

        <!-- Product Image Header -->
        <div id="modal-img-container" class="h-64 bg-orange-50 dark:bg-gray-700 flex items-center justify-center text-8xl relative">
            <!-- Icon will be injected here -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent dark:from-black/80"></div>
        </div>

        <!-- Content -->
        <div class="p-6 sm:p-8">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span id="modal-category" class="text-xs font-bold text-orange-600 bg-orange-100 dark:text-orange-300 dark:bg-orange-900/30 px-2 py-1 rounded uppercase tracking-wider">Category</span>
                    <h2 id="modal-title" class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1 leading-tight">Product Name</h2>
                </div>
                <span id="modal-price" class="text-2xl font-extrabold text-gray-900 dark:text-white">RM 0.00</span>
            </div>

            <p id="modal-desc" class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed mb-6">
                Description goes here...
            </p>

            <!-- NEW: Quantity Selector -->
            <div class="flex items-center justify-between mb-8 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700">
                <span class="font-bold text-gray-700 dark:text-gray-200 text-sm pl-2">Quantity</span>
                <div class="flex items-center gap-4">
                    <button onclick="updateModalQty(-1)" class="w-8 h-8 rounded-full bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 flex items-center justify-center text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-500 active:scale-95 transition shadow-sm">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span id="modal-qty-display" class="font-bold text-lg w-4 text-center dark:text-white">1</span>
                    <button onclick="updateModalQty(1)" class="w-8 h-8 rounded-full bg-white dark:bg-gray-600 border border-gray-200 dark:border-gray-500 flex items-center justify-center text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-500 active:scale-95 transition shadow-sm">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Action Footer -->
            <button id="modal-add-btn" class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 text-lg flex items-center justify-center gap-3">
                <i class="fas fa-plus"></i> Add to Tray
            </button>
        </div>
    </div>
</div>
