<!-- VIEW: REGISTER -->
<div id="view-register" class="hidden h-full flex-col items-center justify-center fade-in p-4 bg-orange-500 dark:bg-gray-900 transition-colors duration-500">
    <div class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-2xl w-full max-w-md transition-colors duration-300">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Account</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Join UniCanteen today</p>
        </div>

        <div id="register-error" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 text-red-700 dark:text-red-300 text-sm rounded-lg flex items-center gap-2 animate-pulse">
            <i class="fas fa-exclamation-circle"></i>
            <span id="register-error-text">Error message</span>
        </div>

        <form onsubmit="handleRegister(event)" class="space-y-4" novalidate>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Full Name</label>
                <input type="text" id="reg-name" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">TARC Email</label>
                <input type="email" id="reg-email" placeholder="@student.tarc.edu.my" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Password</label>
                <input type="password" id="reg-password" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase mb-1">Confirm Password</label>
                <input type="password" id="reg-confirm-password" class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none transition bg-gray-50 dark:bg-gray-700 dark:text-white">
            </div>
            <button type="submit" class="w-full bg-gray-900 dark:bg-gray-700 hover:bg-gray-800 dark:hover:bg-gray-600 text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95 mt-2">
                Register
            </button>
        </form>
        <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
            Already have an account? 
            <button onclick="switchView('login')" class="text-orange-600 dark:text-orange-400 font-bold hover:underline">Sign In</button>
        </div>
    </div>
</div>
