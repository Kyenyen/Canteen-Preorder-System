<!-- VIEW: HOME -->
<div id="view-home" class="hidden h-full flex-col fade-in bg-gray-50 dark:bg-gray-900 overflow-y-auto transition-colors duration-300">
    <!-- Header/Greeting -->
    <div class="bg-white dark:bg-gray-800 px-6 py-8 border-b border-gray-200 dark:border-gray-700 shadow-sm transition-colors duration-300">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-extrabold text-gray-800 dark:text-white mb-1" id="home-greeting">Welcome back!</h2>
            <p class="text-gray-500 dark:text-gray-400">What would you like to eat today?</p>
        </div>
    </div>

    <div class="p-6 max-w-4xl mx-auto w-full space-y-8 pb-20">
        <!-- Active Order Status Widget (Dynamic) -->
        <div id="home-active-order-card" class="hidden bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border-l-8 border-orange-500 cursor-pointer hover:shadow-xl transition transform hover:-translate-y-1" onclick="switchView('history')">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="h-2 w-2 bg-green-500 rounded-full animate-pulse"></span>
                        <p class="text-xs font-bold text-orange-500 dark:text-orange-400 uppercase tracking-wider">Active Order</p>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white" id="home-order-id">Order #...</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1" id="home-order-status">Preparing your meal...</p>
                </div>
                <div class="h-14 w-14 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center text-orange-600 dark:text-orange-400 text-2xl">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>
        </div>

        <!-- Hero/Promo -->
        <div class="relative rounded-2xl overflow-hidden bg-gray-900 dark:bg-black text-white shadow-xl group">
            <div class="absolute inset-0 bg-gradient-to-r from-orange-600 to-red-600 dark:from-orange-800 dark:to-red-900 opacity-90 group-hover:opacity-100 transition duration-500"></div>
            <!-- Decorative elements -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
            <div class="absolute left-10 bottom-10 w-20 h-20 bg-yellow-300 opacity-20 rounded-full blur-xl"></div>

            <div class="relative p-8 sm:p-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div>
                    <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3 inline-block border border-white/30">Promo of the Day</span>
                    <h3 class="text-3xl font-extrabold mb-2">Nasi Lemak Special</h3>
                    <p class="text-orange-100 mb-6 max-w-md text-sm leading-relaxed">Get 10% off when you pre-order before 10 AM. Don't miss out on the crowd favorite!</p>
                    <button onclick="switchView('student')" class="bg-white text-orange-600 font-bold py-3 px-8 rounded-xl shadow-lg hover:bg-gray-50 transition transform active:scale-95 flex items-center gap-2">
                        Order Now <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </div>
                <i class="fas fa-bowl-rice text-9xl opacity-30 rotate-12 transform translate-x-4 translate-y-4 sm:block hidden"></i>
            </div>
        </div>

        <!-- Quick Shortcuts -->
        <div class="grid grid-cols-2 gap-4">
            <div onclick="switchView('student')" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-orange-200 dark:hover:border-orange-700 hover:shadow-md transition cursor-pointer group">
                <div class="h-12 w-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-600 dark:text-blue-400 mb-4 group-hover:scale-110 transition duration-300">
                    <i class="fas fa-book-open text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800 dark:text-white text-lg">Full Menu</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Browse all available items</p>
            </div>
            <div onclick="switchView('history')" class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 hover:border-orange-200 dark:hover:border-orange-700 hover:shadow-md transition cursor-pointer group">
                <div class="h-12 w-12 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-600 dark:text-green-400 mb-4 group-hover:scale-110 transition duration-300">
                    <i class="fas fa-clock-rotate-left text-xl"></i>
                </div>
                <h4 class="font-bold text-gray-800 dark:text-white text-lg">Order History</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Track or re-order</p>
            </div>
        </div>
        
        <!-- Popular Section -->
        <div>
            <h3 class="font-bold text-gray-800 dark:text-white text-lg mb-4 flex items-center gap-2">
                <i class="fas fa-star text-yellow-400"></i> Popular Today
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4" id="home-popular-grid">
                <!-- JS Injected Items -->
            </div>
        </div>
    </div>
</div>
