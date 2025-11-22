<!-- Navigation -->
<nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex justify-between items-center shadow-sm z-50 transition-colors duration-300">
    <div class="flex items-center gap-3" onclick="if(currentUser && currentUser.role === 'student') switchView('home')" class="cursor-pointer">
        <div class="bg-orange-500 text-white p-2 rounded-lg">
            <i class="fas fa-utensils"></i>
        </div>
        <h1 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">UniCanteen</h1>
    </div>
    
    <div class="flex items-center gap-4">
        <!-- Dark Mode Toggle -->
        <button onclick="toggleDarkMode()" class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-yellow-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors focus:outline-none">
            <i class="fas fa-moon dark:hidden"></i>
            <i class="fas fa-sun hidden dark:block"></i>
        </button>

        <!-- Auth Nav -->
        <div id="nav-auth" class="hidden items-center gap-3">
            <!-- Student Nav Items (Hidden for Admin) -->
            <div id="nav-student-links" class="hidden md:flex gap-1 mr-2">
                <button onclick="switchView('home')" id="nav-home-btn" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"><i class="fas fa-home mr-1"></i> Home</button>
                <button onclick="switchView('student')" id="nav-menu-btn" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"><i class="fas fa-burger mr-1"></i> Menu</button>
                <button onclick="switchView('history')" id="nav-history-btn" class="px-3 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"><i class="fas fa-clock-rotate-left mr-1"></i> My Orders</button>
            </div>

            <!-- Admin Nav Items (Hidden for Student) -->
            <div class="hidden md:flex gap-2 mr-4" id="role-switcher">
                <button onclick="switchView('admin')" id="btn-admin" class="px-4 py-2 rounded-md text-sm font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors hidden"><i class="fas fa-fire-burner mr-1"></i> Kitchen View</button>
            </div>

            <div class="flex items-center gap-3 pl-4 border-l border-gray-200 dark:border-gray-700">
                <div class="text-right hidden sm:block">
                    <div id="user-name-display" class="text-sm font-bold text-gray-800 dark:text-white leading-tight">User</div>
                    <div id="user-role-display" class="text-xs text-gray-500 dark:text-gray-400 uppercase">Student</div>
                </div>
                <button onclick="logout()" class="bg-gray-100 dark:bg-gray-700 hover:bg-red-100 dark:hover:bg-red-900 hover:text-red-600 dark:hover:text-red-300 text-gray-600 dark:text-gray-300 w-8 h-8 rounded-full flex items-center justify-center transition-colors"><i class="fas fa-sign-out-alt"></i></button>
            </div>
        </div>
    </div>
</nav>
