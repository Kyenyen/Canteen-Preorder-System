<!-- Cart Backdrop -->
<div id="cart-backdrop" onclick="toggleCart()" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden transition-opacity duration-300 opacity-0 backdrop-blur-sm"></div>

<!-- Checkout Sidebar (Floating Drawer) -->
<div class="w-full md:w-[500px] bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col h-full shadow-2xl z-[110] transform transition-transform duration-300 fixed top-0 right-0 translate-x-full" id="cart-panel">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800">
        <h3 class="font-extrabold text-2xl text-gray-800 dark:text-white">My Tray</h3>
        <button onclick="toggleCart()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors"><i class="fas fa-times text-xl"></i></button>
    </div>
    
    <div class="flex-1 overflow-y-auto p-6 space-y-6">
        <!-- Order Details Section -->
        <div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                    <i class="fas fa-receipt text-orange-600"></i>
                    Order Items
                </h4>
                <button onclick="toggleCart()" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-semibold transition-colors flex items-center gap-1">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Add More</span>
                </button>
            </div>
            <div id="cart-items" class="space-y-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 border border-gray-200 dark:border-gray-600">
                <div id="empty-cart-msg" class="text-center text-gray-300 dark:text-gray-600 py-8 flex flex-col items-center">
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-3">
                        <i class="fas fa-basket-shopping text-3xl"></i>
                    </div>
                    <p class="font-medium">Your tray is empty</p>
                    <p class="text-xs mt-1">Add some delicious food!</p>
                </div>
            </div>
        </div>

        <!-- Order Note Section -->
        <div id="note-section" class="hidden">
            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i class="fas fa-note-sticky text-orange-600"></i>
                Note (Optional)
            </h4>
            <textarea id="order-note" rows="3" class="w-full p-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-orange-500 outline-none dark:text-white resize-none" placeholder="E.g., No onions, extra spicy, separate packaging..."></textarea>
        </div>

        <!-- Pickup Time Section -->
        <div id="pickup-section" class="hidden">
            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i class="fas fa-clock text-orange-600"></i>
                Pickup Time
            </h4>
            <div class="relative">
                <select id="pickup-time" class="w-full p-3 pl-10 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-orange-500 outline-none appearance-none dark:text-white">
                    <!-- Options injected by JS -->
                </select>
                <i class="fas fa-clock absolute left-3 top-3.5 text-gray-400 dark:text-gray-500"></i>
                <i class="fas fa-chevron-down absolute right-3 top-3.5 text-gray-400 dark:text-gray-500 text-xs"></i>
            </div>
        </div>

        <!-- Payment Method Section -->
        <div id="payment-method-section" class="hidden">
            <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                <i class="fas fa-credit-card text-orange-600"></i>
                Payment Method
            </h4>
            <div class="space-y-2">
                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-white dark:bg-gray-700 group">
                    <input type="radio" name="payment_method" value="ewallet" class="w-4 h-4 text-orange-600 focus:ring-orange-500 border-gray-300" checked>
                    <div class="ml-3 flex-1">
                        <span class="block text-sm font-bold text-gray-800 dark:text-white">TARC eWallet</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Balance: RM 50.00</span>
                    </div>
                    <i class="fas fa-wallet text-gray-400 group-hover:text-orange-500"></i>
                </label>

                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-white dark:bg-gray-700 group">
                    <input type="radio" name="payment_method" value="duitnow" class="w-4 h-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                    <div class="ml-3 flex-1">
                        <span class="block text-sm font-bold text-gray-800 dark:text-white">DuitNow QR</span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">Scan to pay</span>
                    </div>
                    <i class="fas fa-qrcode text-gray-400 group-hover:text-orange-500"></i>
                </label>

                <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-white dark:bg-gray-700 group">
                    <input type="radio" name="payment_method" value="card" class="w-4 h-4 text-orange-600 focus:ring-orange-500 border-gray-300">
                    <div class="ml-3 flex-1">
                        <span class="block text-sm font-bold text-gray-800 dark:text-white">Credit / Debit Card</span>
                    </div>
                    <i class="fas fa-credit-card text-gray-400 group-hover:text-orange-500"></i>
                </label>
            </div>
        </div>

        <!-- Order Summary -->
        <div id="summary-section" class="hidden bg-orange-50 dark:bg-orange-900/20 rounded-xl p-4 border border-orange-200 dark:border-orange-800">
            <h4 class="text-sm font-bold text-orange-800 dark:text-orange-300 mb-3">Order Summary</h4>
            <div class="space-y-2">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span id="cart-subtotal">RM 0.00</span>
                </div>
                <div class="flex justify-between text-xl font-extrabold text-gray-900 dark:text-white border-t border-orange-200 dark:border-orange-700 pt-2">
                    <span>Total</span>
                    <span id="cart-total">RM 0.00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <button onclick="openPaymentModal()" id="btn-checkout" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2" disabled>
            <i class="fas fa-shopping-cart"></i>
            <span>Place Order</span>
        </button>
    </div>
</div>

<!-- Cart Toggle Button (Floating Action Button) -->
<button id="fab-cart-btn" onclick="toggleCart()" class="hidden fixed bottom-6 right-6 bg-orange-600 hover:bg-orange-700 text-white w-16 h-16 rounded-full shadow-2xl flex items-center justify-center z-30 active:scale-90 transition-all duration-300 hover:scale-105 dark:shadow-orange-900/50">
    <div class="relative">
        <i class="fas fa-basket-shopping text-2xl"></i>
        <span id="mobile-cart-count" class="absolute -top-3 -right-3 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center border-2 border-orange-600 hidden">0</span>
    </div>
</button>
