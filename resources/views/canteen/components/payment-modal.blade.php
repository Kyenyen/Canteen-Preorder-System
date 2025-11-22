<!-- NEW: Payment Modal (Multi-Step) -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden items-center justify-center p-4 fade-in backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all scale-100 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="bg-gray-50 dark:bg-gray-750 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center shrink-0">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white" id="payment-modal-title">Payment Details</h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Content Area (Scrollable) -->
        <div id="payment-content" class="p-6 overflow-y-auto">
            <!-- STEP 1: Select Method -->
            <div id="payment-step-1" class="block">
                <div class="text-center mb-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Total Amount</p>
                    <h2 id="payment-total-display" class="text-4xl font-extrabold text-gray-900 dark:text-white">RM 0.00</h2>
                </div>

                <div class="space-y-3">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Select Payment Method</p>
                    
                    <label class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-white dark:bg-gray-700 group">
                        <input type="radio" name="payment_method" value="ewallet" class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300" checked>
                        <div class="ml-4 flex-1">
                            <span class="block text-sm font-bold text-gray-800 dark:text-white group-hover:text-orange-600 transition">TARC eWallet</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">Balance: RM 50.00</span>
                        </div>
                        <i class="fas fa-wallet text-gray-400 group-hover:text-orange-500 text-xl"></i>
                    </label>

                    <label class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-white dark:bg-gray-700 group">
                        <input type="radio" name="payment_method" value="duitnow" class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                        <div class="ml-4 flex-1">
                            <span class="block text-sm font-bold text-gray-800 dark:text-white group-hover:text-orange-600 transition">DuitNow QR</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400">Scan to pay</span>
                        </div>
                        <i class="fas fa-qrcode text-gray-400 group-hover:text-orange-500 text-xl"></i>
                    </label>

                    <label class="flex items-center p-4 border border-gray-200 dark:border-gray-700 rounded-xl cursor-pointer hover:border-orange-500 dark:hover:border-orange-500 transition bg-white dark:bg-gray-700 group">
                        <input type="radio" name="payment_method" value="card" class="w-5 h-5 text-orange-600 focus:ring-orange-500 border-gray-300">
                        <div class="ml-4 flex-1">
                            <span class="block text-sm font-bold text-gray-800 dark:text-white group-hover:text-orange-600 transition">Credit / Debit Card</span>
                        </div>
                        <i class="fas fa-credit-card text-gray-400 group-hover:text-orange-500 text-xl"></i>
                    </label>
                </div>

                <button onclick="goToPaymentStep2()" class="w-full mt-8 bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex items-center justify-center gap-2">
                    <span>Next</span>
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>

            <!-- STEP 2: Receipt/Order Summary -->
            <div id="payment-step-2" class="hidden">
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6 border border-gray-200 dark:border-gray-700">
                    <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 border-b border-gray-200 dark:border-gray-600 pb-2">Order Summary</h4>
                    <div id="payment-summary-items" class="space-y-2 text-sm mb-4">
                        <!-- Injected items -->
                    </div>
                    <div class="flex justify-between border-t border-gray-200 dark:border-gray-600 pt-2 font-bold text-gray-900 dark:text-white">
                        <span>Total</span>
                        <span id="payment-summary-total">RM 0.00</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-800 mb-6">
                    <span class="text-xs text-blue-700 dark:text-blue-300 font-medium">Payment Method</span>
                    <span id="payment-summary-method" class="text-xs font-bold text-blue-800 dark:text-blue-200 uppercase">EWALLET</span>
                </div>

                <div class="flex gap-3">
                    <button onclick="showPaymentStep(1)" class="w-1/3 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-bold py-3 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                        Back
                    </button>
                    <button onclick="goToPaymentStep3()" class="w-2/3 bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95">
                        Checkout
                    </button>
                </div>
            </div>

            <!-- STEP 3: Action (QR or Input) -->
            <div id="payment-step-3" class="hidden">
                
                <!-- 1. QR Code View (DuitNow) -->
                <div id="payment-view-qr" class="hidden flex-col items-center text-center">
                    <div class="bg-white p-4 rounded-xl shadow-sm mb-4 border border-gray-200">
                        <div class="w-48 h-48 bg-gray-900 flex items-center justify-center text-white">
                            <i class="fas fa-qrcode text-6xl"></i>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-6">Scan this QR code with your preferred banking app to complete the payment.</p>
                    <button onclick="processFinalPayment('qr')" id="btn-confirm-qr" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95">
                        I have Scanned
                    </button>
                </div>

                <!-- 2. Card Form View -->
                <div id="payment-view-card" class="hidden space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Card Number (16 Digits)</label>
                        <input type="text" id="card-number" 
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none dark:bg-gray-700 dark:text-white tracking-widest font-mono" 
                            placeholder="0000 0000 0000 0000" 
                            maxlength="19" 
                            inputmode="numeric"
                            oninput="formatCardNumber(this)">
                    </div>

                    <div class="flex gap-3">
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Expiry Date</label>
                            <input type="text" id="card-expiry"
                                class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none dark:bg-gray-700 dark:text-white tracking-widest text-center" 
                                placeholder="MM/YY" 
                                maxlength="5" 
                                oninput="formatExpiry(this)">
                        </div>
                        <div class="w-1/2">
                            <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">CVV</label>
                            <input type="password" id="card-cvv"
                                class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none dark:bg-gray-700 dark:text-white tracking-widest text-center" 
                                placeholder="123" 
                                maxlength="3" 
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3)">
                        </div>
                    </div>
                     
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Card PIN (6 Digits)</label>
                        <input type="password" id="card-pin"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none dark:bg-gray-700 dark:text-white tracking-widest" 
                            placeholder="******" 
                            maxlength="6" 
                            inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                    </div>

                    <button onclick="processFinalPayment('card')" id="btn-confirm-card" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95">
                        Pay with Card
                    </button>
                </div>

                <!-- 3. eWallet Form View -->
                <div id="payment-view-ewallet" class="hidden space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Phone Number</label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border border-r-0 border-gray-300 rounded-l-xl dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                                +60
                            </span>
                            <input type="text" id="ewallet-phone" 
                                class="rounded-none rounded-r-xl bg-gray-50 border border-gray-300 text-gray-900 focus:ring-orange-500 focus:border-orange-500 block flex-1 min-w-0 w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                placeholder="12 345 6789"
                                inputmode="numeric"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                    </div>
                     <div>
                        <label class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">6-Digit PIN</label>
                        <input type="password" id="ewallet-pin"
                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-orange-500 outline-none dark:bg-gray-700 dark:text-white tracking-widest" 
                            placeholder="******" 
                            maxlength="6" 
                            inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                    </div>
                    <button onclick="processFinalPayment('ewallet')" id="btn-confirm-ewallet" class="w-full mt-4 bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95">
                        Pay with eWallet
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
