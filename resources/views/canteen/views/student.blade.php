<!-- VIEW: STUDENT (MENU) -->
<div id="view-student" class="hidden h-full flex-col fade-in relative">
    <!-- Menu Grid -->
    <div class="flex-1 overflow-y-auto p-6 bg-gray-50 dark:bg-gray-900 w-full transition-colors duration-300">
        
        <!-- Categories -->
        <div class="flex gap-3 mb-6 overflow-x-auto pb-2 scrollbar-hide">
            <button onclick="filterMenu('All')" id="cat-All" class="category-btn px-6 py-2 bg-gray-900 dark:bg-gray-700 text-white rounded-full text-sm font-bold shadow-md transition-all transform hover:scale-105">All</button>
            <button onclick="filterMenu('Breakfast')" id="cat-Breakfast" class="category-btn px-6 py-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-full text-sm font-bold shadow-sm hover:shadow-md transition-all transform hover:scale-105">Breakfast</button>
            <button onclick="filterMenu('Lunch')" id="cat-Lunch" class="category-btn px-6 py-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-full text-sm font-bold shadow-sm hover:shadow-md transition-all transform hover:scale-105">Lunch</button>
            <button onclick="filterMenu('Beverage')" id="cat-Beverage" class="category-btn px-6 py-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-full text-sm font-bold shadow-sm hover:shadow-md transition-all transform hover:scale-105">Drinks</button>
        </div>

        <!-- Grid -->
        <div id="menu-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 pb-20">
            <!-- Menu items injected by JS -->
        </div>
    </div>
</div>
