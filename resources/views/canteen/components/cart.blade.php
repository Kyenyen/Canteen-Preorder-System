<!-- Cart Backdrop -->
<div id="cart-backdrop" onclick="toggleCart()" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden transition-opacity duration-300 opacity-0 backdrop-blur-sm"></div>

<!-- Cart Sidebar (Simple View) -->
<div class="w-full md:w-[400px] bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 flex flex-col h-full shadow-2xl z-[110] transform transition-transform duration-300 fixed top-0 right-0 translate-x-full" id="cart-panel">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-white dark:bg-gray-800">
        <h3 class="font-extrabold text-2xl text-gray-800 dark:text-white">My Tray</h3>
        <div class="flex items-center gap-3">
            <button id="cart-edit-btn" onclick="toggleCartEditMode()" class="text-orange-600 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 text-sm font-semibold transition-colors hidden">
                <i class="fas fa-pen mr-1"></i>Edit
            </button>
            <button onclick="toggleCart()" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-white transition-colors"><i class="fas fa-times text-xl"></i></button>
        </div>
    </div>
    
    <div class="flex-1 overflow-y-auto p-6">
        <div id="cart-items-simple" class="space-y-3">
            <!-- Cart items injected by JS -->
        </div>
    </div>

    <div class="p-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 space-y-4">
        <!-- Total -->
        <div class="flex justify-between items-center">
            <span class="text-gray-600 dark:text-gray-400 font-medium">Total</span>
            <span id="cart-simple-total" class="text-2xl font-extrabold text-gray-900 dark:text-white">RM 0.00</span>
        </div>
        
        <!-- Checkout Button -->
        <button onclick="goToCheckout()" id="btn-cart-checkout" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2" disabled>
            <i class="fas fa-shopping-bag"></i>
            <span>Checkout</span>
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
