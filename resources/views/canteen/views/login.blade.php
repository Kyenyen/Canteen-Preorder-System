<!-- VIEW: LOGIN -->
<div id="view-login" class="h-full flex flex-col items-center justify-center fade-in p-4 bg-orange-500 dark:bg-gray-900 transition-colors duration-500">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300">
        <div class="text-center mb-6">
            <div class="bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                <i class="fas fa-burger"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">UniCanteen</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Skip the queue, eat better.</p>
        </div>

        <!-- Login Error Message -->
        <div id="login-error" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-center gap-2 animate-pulse">
            <i class="fas fa-exclamation-circle"></i>
            <span id="login-error-text">Invalid credentials</span>
        </div>

        <form onsubmit="handleLogin(event)" class="space-y-5" novalidate>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">TARC Email Address</label>
                <input type="email" id="login-email" placeholder="name@student.tarc.edu.my" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400">
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    Students: <span class="font-mono text-orange-600 dark:text-orange-400">@student.tarc.edu.my</span><br>
                    Admin: <span class="font-mono text-orange-600 dark:text-orange-400">@admin.tarc.edu.my</span>
                </p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Password</label>
                <input type="password" id="login-password" value="password" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white">
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 text-right">Min. 6 characters</p>
            </div>
            <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-600 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 text-lg">
                Sign In
            </button>
        </form>
        <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Don't have an account? 
            <button onclick="switchView('register')" class="text-orange-600 dark:text-orange-400 font-bold hover:underline">Sign Up</button>
        </div>
    </div>
</div>
