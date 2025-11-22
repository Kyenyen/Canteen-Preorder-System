<!-- Cart Backdrop -->
<div id="cart-backdrop" onclick="toggleCart()" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden transition-opacity duration-300 opacity-0 backdrop-blur-sm"></div>

<!-- Cart Sidebar (Floating Drawer) -->
<div class="w-full md:w-96 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col h-full shadow-2xl z-[110] transform transition-transform duration-300 fixed top-0 right-0 translate-x-full" id="cart-panel">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800">
        <h3 class="font-extrabold text-2xl text-gray-800 dark:text-white">My Tray</h3>
        <button onclick="toggleCart()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors"><i class="fas fa-times text-xl"></i></button>
    </div>
    
    <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-6">
        <div id="empty-cart-msg" class="text-center text-gray-300 dark:text-gray-600 mt-20 flex flex-col items-center">
            <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-full mb-4">
                <i class="fas fa-basket-shopping text-4xl"></i>
            </div>
            <p class="font-medium">Your tray is empty</p>
            <p class="text-sm mt-2">Add some delicious food!</p>
        </div>
    </div>

    <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <div class="flex justify-between mb-3 text-gray-600 dark:text-gray-400 font-medium">
            <span>Subtotal</span>
            <span id="cart-subtotal">RM 0.00</span>
        </div>
        <div class="flex justify-between mb-6 text-2xl font-extrabold text-gray-900 dark:text-white">
            <span>Total</span>
            <span id="cart-total">RM 0.00</span>
        </div>
        
        <div class="mb-4">
            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Pickup Time</label>
            <div class="relative">
                <select id="pickup-time" class="w-full p-3 pl-10 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl text-sm font-medium focus:ring-2 focus:ring-orange-500 outline-none appearance-none dark:text-white">
                    <!-- Options injected by JS -->
                </select>
                <i class="fas fa-clock absolute left-3 top-3.5 text-gray-400 dark:text-gray-500"></i>
            </div>
        </div>

        <button onclick="openPaymentModal()" id="btn-checkout" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none" disabled>
            Proceed to Payment
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
