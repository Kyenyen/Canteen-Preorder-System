import './bootstrap';
import '../css/app.css';

// --- Mock Data ---
const menuItems = [
    { 
        id: 1, 
        name: "Nasi Lemak Special", 
        price: 5.50, 
        category: "Lunch", 
        image: "text-orange-500 bg-orange-100 fa-bowl-rice", 
        description: "Our signature dish featuring fragrant coconut milk rice, served with spicy sambal, crispy anchovies, toasted peanuts, cool cucumber slices, and a whole hard-boiled egg." 
    },
    { 
        id: 2, 
        name: "Chicken Rice Set", 
        price: 6.00, 
        category: "Lunch", 
        image: "text-yellow-600 bg-yellow-100 fa-drumstick-bite", 
        description: "Succulent poached chicken served with aromatic seasoned rice, clear soup, and our homemade garlic-chili dipping sauce." 
    },
    { 
        id: 3, 
        name: "Tuna Sandwich", 
        price: 3.50, 
        category: "Breakfast", 
        image: "text-green-600 bg-green-100 fa-bread-slice", 
        description: "Freshly prepared tuna mayonnaise filling with crunchy lettuce and tomatoes sandwiched between soft wholemeal bread." 
    },
    { 
        id: 4, 
        name: "Mee Goreng Mamak", 
        price: 4.50, 
        category: "Lunch", 
        image: "text-red-500 bg-red-100 fa-utensils", 
        description: "Spicy fried yellow noodles wok-tossed with tofu, potatoes, bean sprouts, and egg in a rich sweet and spicy sauce." 
    },
    { 
        id: 5, 
        name: "Iced Milo", 
        price: 2.50, 
        category: "Beverage", 
        image: "text-brown-600 bg-stone-200 fa-glass-water", 
        description: "Malaysia's favorite chocolate malt drink, served ice-cold and extra kaw." 
    },
    { 
        id: 6, 
        name: "Fresh Watermelon Juice", 
        price: 3.00, 
        category: "Beverage", 
        image: "text-pink-500 bg-pink-100 fa-wine-glass", 
        description: "100% pure refreshing watermelon juice, freshly blended with no added sugar." 
    },
];

// --- State Management ---
let cart = [];
let orders = []; 
let currentUser = null;
let currentCategory = 'All';
let currentModalQty = 1;
let isEditMode = false;
let isCartEditMode = false; 

// --- Init ---
window.onload = function() {
    // Check Dark Mode Preference
    if (localStorage.getItem('darkMode') === 'true' || 
       (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    switchView('login');
    renderMenu();
    generatePickupTimes();
};

window.toggleDarkMode = function() {
    const html = document.documentElement;
    if (html.classList.contains('dark')) {
        html.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    } else {
        html.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    }
};

// --- Time Slot Generation ---
function generatePickupTimes() {
    const select = document.getElementById('pickup-time');
    const checkoutSelect = document.getElementById('checkout-pickup-time');
    
    if (select) {
        select.innerHTML = ''; 
    }
    if (checkoutSelect) {
        checkoutSelect.innerHTML = '';
    }

    const now = new Date();
    const startHour = 8;
    const endHour = 17;
    const options = [];

    for (let hour = startHour; hour < endHour; hour++) {
        for (let min = 0; min < 60; min += 30) {
            const slotTime = new Date();
            slotTime.setHours(hour, min, 0, 0);

            const bufferTime = new Date(now.getTime() + 15 * 60000);

            if (slotTime > bufferTime) {
                const timeString = slotTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
                options.push({ value: timeString, text: timeString, disabled: false });
            }
        }
    }
    
    const closingTime = new Date();
    closingTime.setHours(endHour, 0, 0, 0);
    if (closingTime > new Date(now.getTime() + 15 * 60000)) {
         const timeString = closingTime.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
         options.push({ value: timeString, text: timeString, disabled: false });
    }

    if (options.length === 0) {
        options.push({ value: '', text: 'Canteen Closed (Next Day)', disabled: true });
    }

    // Populate both selects
    [select, checkoutSelect].forEach(selectElement => {
        if (selectElement) {
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.text = opt.text;
                option.disabled = opt.disabled;
                if (opt.disabled) option.selected = true;
                selectElement.appendChild(option);
            });
        }
    });
}

// --- Helper Functions ---
function formatCurrency(amount) { return 'RM ' + amount.toFixed(2); }

function showToast(msg, moveFab = true) {
    const toast = document.getElementById('toast');
    const fabBtn = document.getElementById('fab-cart-btn');
    
    document.getElementById('toast-message').innerText = msg;
    
    toast.classList.remove('translate-y-40', 'opacity-0');
    
    if (moveFab && !fabBtn.classList.contains('hidden')) {
        fabBtn.style.transform = 'translateY(-4rem)'; 
    }

    setTimeout(() => {
        toast.classList.add('translate-y-40', 'opacity-0');
        if (!fabBtn.classList.contains('hidden')) {
            fabBtn.style.transform = 'translateY(0)'; 
        }
    }, 3000);
}

// --- Auth Logic ---
window.handleLogin = function(e) {
    e.preventDefault();
    
    const emailInput = document.getElementById('login-email');
    const passInput = document.getElementById('login-password');
    const errorDiv = document.getElementById('login-error');
    
    const email = emailInput.value.trim().toLowerCase();
    const password = passInput.value.trim();

    const studentDomain = '@student.tarc.edu.my';
    const adminDomain = '@admin.tarc.edu.my';

    if (!email || !email.includes('@')) {
        showLoginError("Please enter a valid email address.");
        return;
    }
    if (!password || password.length < 6) {
        showLoginError("Password must be at least 6 characters.");
        return;
    }

    let role = '';
    if (email.endsWith(studentDomain)) {
        role = 'student';
    } else if (email.endsWith(adminDomain)) {
        role = 'admin';
    } else {
        showLoginError(`Email must end with ${studentDomain} or ${adminDomain}`);
        return;
    }

    errorDiv.classList.add('hidden');

    const name = email.split('@')[0];
    currentUser = { name: name, role: role, email: email };
    
    document.getElementById('user-name-display').innerText = name;
    document.getElementById('user-role-display').innerText = role;
    document.getElementById('nav-auth').classList.remove('hidden');
    document.getElementById('nav-auth').classList.add('flex');

    const navStudent = document.getElementById('nav-student-links');
    const btnAdmin = document.getElementById('btn-admin');

    if (role === 'admin') {
        navStudent.classList.remove('md:flex');
        navStudent.classList.add('hidden');
        
        btnAdmin.classList.remove('hidden');
        loadAllOrders(); 
        switchView('admin');
        showToast('Welcome back, Admin!');
    } else {
        navStudent.classList.add('md:flex');
        navStudent.classList.remove('hidden');
        
        btnAdmin.classList.add('hidden');
        loadUserOrders(); 
        switchView('home'); 
        showToast(`Welcome, ${name}!`);
        generatePickupTimes();
    }
};

window.handleRegister = function(e) {
    e.preventDefault();

    const nameInput = document.getElementById('reg-name');
    const emailInput = document.getElementById('reg-email');
    const passInput = document.getElementById('reg-password');
    const confirmPassInput = document.getElementById('reg-confirm-password');
    const errorDiv = document.getElementById('register-error');
    const errorText = document.getElementById('register-error-text');

    const name = nameInput.value.trim();
    const email = emailInput.value.trim().toLowerCase();
    const password = passInput.value.trim();
    const confirmPassword = confirmPassInput.value.trim();

    const studentDomain = '@student.tarc.edu.my';
    const adminDomain = '@admin.tarc.edu.my';

    if (!name) {
        errorText.innerText = "Please enter your full name.";
        errorDiv.classList.remove('hidden');
        return;
    }

    if (!email.endsWith(studentDomain) && !email.endsWith(adminDomain)) {
        errorText.innerText = `Email must end with ${studentDomain} or ${adminDomain}`;
        errorDiv.classList.remove('hidden');
        return;
    }

    if (password.length < 6) {
        errorText.innerText = "Password must be at least 6 characters.";
        errorDiv.classList.remove('hidden');
        return;
    }

    if (password !== confirmPassword) {
        errorText.innerText = "Passwords do not match.";
        errorDiv.classList.remove('hidden');
        return;
    }

    errorDiv.classList.add('hidden');
    showToast('Registration Successful! Please login.');
    switchView('login');
    document.getElementById('login-email').value = email;
};

function showLoginError(msg) {
    const errorDiv = document.getElementById('login-error');
    const errorText = document.getElementById('login-error-text');
    errorText.innerText = msg;
    errorDiv.classList.remove('hidden');
}

window.logout = function() {
    currentUser = null;
    cart = [];
    updateCartUI();
    document.getElementById('nav-auth').classList.add('hidden');
    document.getElementById('nav-auth').classList.remove('flex');
    document.getElementById('login-email').value = '';
    document.getElementById('login-password').value = '';
    document.getElementById('login-error').classList.add('hidden');
    document.getElementById('fab-cart-btn').classList.add('hidden');
    switchView('login');
};

// --- Payment Logic ---
window.openPaymentModal = function() {
    // Check if we're on checkout page or using cart
    const checkoutMethod = document.querySelector('input[name="checkout_payment_method"]:checked');
    const cartMethod = document.querySelector('input[name="payment_method"]:checked');
    const method = checkoutMethod ? checkoutMethod.value : (cartMethod ? cartMethod.value : 'ewallet');
    
    // Show appropriate payment view
    document.getElementById('payment-view-qr').classList.add('hidden');
    document.getElementById('payment-view-qr').classList.remove('flex');
    document.getElementById('payment-view-card').classList.add('hidden');
    document.getElementById('payment-view-ewallet').classList.add('hidden');
    
    if(method === 'duitnow') {
        document.getElementById('payment-view-qr').classList.remove('hidden');
        document.getElementById('payment-view-qr').classList.add('flex');
    } else if (method === 'card') {
        document.getElementById('payment-view-card').classList.remove('hidden');
        setTimeout(() => document.getElementById('card-number').focus(), 100);
    } else if (method === 'ewallet') {
        document.getElementById('payment-view-ewallet').classList.remove('hidden');
        setTimeout(() => document.getElementById('ewallet-phone').focus(), 100);
    }
    
    const modal = document.getElementById('payment-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.closePaymentModal = function() {
    const modal = document.getElementById('payment-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

window.formatExpiry = function(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    input.value = value;
    input.classList.remove('shake');
};

window.formatCardNumber = function(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.slice(0, 16);
    let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
    input.value = formattedValue;
    input.classList.remove('shake');
};

function isExpiryValid(expiryStr) {
    if (!/^\d{2}\/\d{2}$/.test(expiryStr)) return false;
    
    const parts = expiryStr.split('/');
    const month = parseInt(parts[0], 10);
    const year = parseInt('20' + parts[1], 10);
    
    if (month < 1 || month > 12) return false;
    
    const now = new Date();
    const currentYear = now.getFullYear();
    const currentMonth = now.getMonth() + 1; 
    
    if (year < currentYear) return false;
    if (year === currentYear && month < currentMonth) return false;
    
    return true;
}

window.processFinalPayment = function(type) {
    let isValid = true;
    
    const shakeInput = (id) => {
        const el = document.getElementById(id);
        el.classList.add('shake');
        setTimeout(() => el.classList.remove('shake'), 500);
        isValid = false;
    };

    if (type === 'card') {
        const rawNum = document.getElementById('card-number').value;
        const num = rawNum.replace(/\s/g, '');
        const expiry = document.getElementById('card-expiry').value;
        const cvv = document.getElementById('card-cvv').value;
        const pin = document.getElementById('card-pin').value;
        
        if (!rawNum) {
            showToast('Card Number cannot be empty.', false);
            shakeInput('card-number');
            return;
        }
        if (!expiry) {
            showToast('Expiry Date cannot be empty.', false);
            shakeInput('card-expiry');
            return;
        }
        if (!cvv) {
            showToast('CVV cannot be empty.', false);
            shakeInput('card-cvv');
            return;
        }
        if (!pin) {
            showToast('PIN cannot be empty.', false);
            shakeInput('card-pin');
            return;
        }

        if (num.length !== 16) {
            showToast('Card Number must be 16 digits.', false);
            shakeInput('card-number');
            return;
        }
        if (!isExpiryValid(expiry)) {
            showToast('Invalid Expiry Date (MM/YY).', false);
            shakeInput('card-expiry');
            return;
        }
        if (cvv.length !== 3) {
            showToast('CVV must be 3 digits.', false);
            shakeInput('card-cvv');
            return;
        }
        if (pin.length !== 6) {
            showToast('Card PIN must be 6 digits.', false);
            shakeInput('card-pin');
            return;
        }

    } else if (type === 'ewallet') {
        const phone = document.getElementById('ewallet-phone').value;
        const pin = document.getElementById('ewallet-pin').value;

        if (!phone) {
            showToast('Phone Number cannot be empty.', false);
            shakeInput('ewallet-phone');
            return;
        }
        if (!pin) {
            showToast('PIN cannot be empty.', false);
            shakeInput('ewallet-pin');
            return;
        }

        if (phone.length < 9) {
            showToast('Invalid Phone Number.', false);
            shakeInput('ewallet-phone');
            return;
        }
        if (pin.length !== 6) {
            showToast('eWallet PIN must be 6 digits.', false);
            shakeInput('ewallet-pin');
            return;
        }
    }

    if (!isValid) return;

    let btnId = 'btn-confirm-qr';
    if (type === 'card') btnId = 'btn-confirm-card';
    if (type === 'ewallet') btnId = 'btn-confirm-ewallet';

    const btn = document.getElementById(btnId);
    
    const originalText = btn.innerText;
    btn.innerText = "Processing...";
    btn.disabled = true;
    btn.classList.add('opacity-75', 'cursor-not-allowed');
    
    setTimeout(() => {
        placeOrder();
        btn.innerText = originalText;
        btn.disabled = false;
        btn.classList.remove('opacity-75', 'cursor-not-allowed');
        closePaymentModal();
        
        if(document.getElementById('card-number')) document.getElementById('card-number').value = '';
        if(document.getElementById('card-pin')) document.getElementById('card-pin').value = '';
        if(document.getElementById('card-cvv')) document.getElementById('card-cvv').value = '';
        if(document.getElementById('card-expiry')) document.getElementById('card-expiry').value = '';
        if(document.getElementById('ewallet-pin')) document.getElementById('ewallet-pin').value = '';
        if(document.getElementById('ewallet-phone')) document.getElementById('ewallet-phone').value = '';
    }, 1500);
};

// --- Render Functions ---
function renderHome() {
    document.getElementById('home-greeting').innerText = `Welcome back, ${currentUser.name}!`;
    
    const myOrders = orders.filter(o => o.studentName === currentUser.name);
    const active = myOrders.find(o => ['pending', 'ready'].includes(o.status));
    
    const activeCard = document.getElementById('home-active-order-card');
    if (active) {
        activeCard.classList.remove('hidden');
        document.getElementById('home-order-id').innerText = active.id;
        const statusText = active.status === 'ready' ? 'Ready for Pickup!' : 'Preparing your meal...';
        document.getElementById('home-order-status').innerText = statusText;
        if(active.status === 'ready') {
            activeCard.classList.replace('border-orange-500', 'border-green-500');
        } else {
            activeCard.classList.replace('border-green-500', 'border-orange-500');
        }
    } else {
        activeCard.classList.add('hidden');
    }

    const popularGrid = document.getElementById('home-popular-grid');
    popularGrid.innerHTML = menuItems.slice(0, 3).map(item => `
        <div onclick="openProductDetail(${item.id})" class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 flex items-center gap-4 shadow-sm hover:shadow-md transition cursor-pointer">
            <div class="h-12 w-12 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-center text-2xl">
                <i class="fas ${item.image.split(' ')[3]} ${item.image.split(' ')[0]}"></i>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 dark:text-gray-100 text-sm">${item.name}</h4>
                <span class="text-orange-600 dark:text-orange-400 font-bold text-xs">${formatCurrency(item.price)}</span>
            </div>
            <div class="ml-auto h-8 w-8 rounded-full bg-orange-50 dark:bg-orange-900/50 text-orange-600 dark:text-orange-400 flex items-center justify-center">
                <i class="fas fa-chevron-right text-xs"></i>
            </div>
        </div>
    `).join('');
}

window.filterMenu = function(category) {
    currentCategory = category;
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.className = 'category-btn px-6 py-2 bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 rounded-full text-sm font-bold shadow-sm hover:shadow-md transition-all transform hover:scale-105';
    });
    const activeBtn = document.getElementById(`cat-${category}`);
    if(activeBtn) activeBtn.className = 'category-btn px-6 py-2 bg-gray-900 dark:bg-gray-600 text-white rounded-full text-sm font-bold shadow-md transition-all transform hover:scale-105';
    renderMenu();
};

function renderMenu() {
    const grid = document.getElementById('menu-grid');
    const itemsToRender = currentCategory === 'All' ? menuItems : menuItems.filter(item => item.category === currentCategory);

    if(itemsToRender.length === 0) {
        grid.innerHTML = `<div class="col-span-full text-center py-10 text-gray-400 dark:text-gray-500">No items found in ${currentCategory}.</div>`;
        return;
    }

    grid.innerHTML = itemsToRender.map(item => {
        const cartItem = cart.find(c => c.id === item.id);
        const inCart = cartItem ? cartItem.qty : 0;
        
        return `
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 p-5 flex flex-col border border-gray-100 dark:border-gray-700 group relative">
            <div onclick="openProductDetail(${item.id})" class="cursor-pointer">
                <div class="h-40 rounded-xl ${item.image.split(' ').slice(1).join(' ')} flex items-center justify-center mb-4 text-5xl transform group-hover:scale-110 transition-transform duration-300">
                    <i class="fas ${item.image.split(' ')[3]} ${item.image.split(' ')[0]}"></i>
                </div>
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs text-orange-500 dark:text-orange-400 font-bold uppercase tracking-wider">${item.category}</span>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 leading-tight mt-1">${item.name}</h3>
                    </div>
                    <span class="font-extrabold text-lg text-gray-900 dark:text-white">${formatCurrency(item.price)}</span>
                </div>
            </div>
            <div class="mt-4">
                ${inCart === 0 ? `
                    <button onclick="addToCartFromMenu(${item.id})" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2.5 rounded-lg transition-colors flex items-center justify-center">
                        <i class="fas fa-plus"></i>
                    </button>
                ` : `
                    <div class="flex items-center gap-2">
                        <button onclick="decreaseFromMenu(${item.id})" class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 text-gray-600 dark:text-gray-300 font-bold py-2.5 rounded-lg transition-colors flex items-center justify-center">
                            <i class="fas fa-minus"></i>
                        </button>
                        <div class="flex-1 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 font-bold py-2.5 rounded-lg flex items-center justify-center border-2 border-orange-200 dark:border-orange-800">
                            ${inCart}
                        </div>
                        <button onclick="increaseFromMenu(${item.id})" class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 dark:hover:text-green-400 text-gray-600 dark:text-gray-300 font-bold py-2.5 rounded-lg transition-colors flex items-center justify-center">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                `}
            </div>
        </div>
    `}).join('');
}

window.openProductDetail = function(id) {
    const item = menuItems.find(i => i.id === id);
    if (!item) return;
    
    currentModalQty = 1;
    
    const modal = document.getElementById('product-modal');
    const imgContainer = document.getElementById('modal-img-container');
    
    const iconClasses = item.image.split(' ');
    const textColor = iconClasses[0];
    const bgColor = iconClasses[1];
    const iconName = iconClasses[3];
    
    imgContainer.innerHTML = `
        <i class="fas ${iconName} ${textColor} relative z-10"></i>
        <div class="absolute inset-0 ${bgColor}"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent dark:from-black/80"></div>
    `;
    
    document.getElementById('modal-category').innerText = item.category;
    document.getElementById('modal-title').innerText = item.name;
    document.getElementById('modal-price').innerText = formatCurrency(item.price);
    document.getElementById('modal-desc').innerText = item.description;
    document.getElementById('modal-qty-display').innerText = currentModalQty;
    
    const addBtn = document.getElementById('modal-add-btn');
    addBtn.onclick = () => {
        addToCart(item.id, currentModalQty);
        showToast(`${currentModalQty}x ${item.name} added to tray`);
        closeProductDetail();
    };
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.closeProductDetail = function() {
    const modal = document.getElementById('product-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    currentModalQty = 1;
};

window.updateModalQty = function(change) {
    currentModalQty += change;
    if (currentModalQty < 1) currentModalQty = 1;
    document.getElementById('modal-qty-display').innerText = currentModalQty;
};

function addToCart(id, quantity = 1) {
    const item = menuItems.find(i => i.id === id);
    const existing = cart.find(i => i.id === id);
    if (existing) {
        existing.qty += quantity;
    } else {
        cart.push({ ...item, qty: quantity });
    }
    updateCartUI();
}

window.addToCartFromMenu = function(id) {
    const item = menuItems.find(i => i.id === id);
    if (item) {
        addToCart(id, 1);
        renderMenu();
        showToast(`${item.name} added to tray`);
    }
};

window.increaseFromMenu = function(id) {
    updateCartQty(id, 1);
    renderMenu();
};

window.decreaseFromMenu = function(id) {
    const item = cart.find(i => i.id === id);
    if (item) {
        if (item.qty === 1) {
            // Directly remove without confirmation
            removeFromCart(id);
        } else {
            item.qty -= 1;
            updateCartUI();
        }
        renderMenu();
    }
};

function removeFromCart(id) {
    cart = cart.filter(i => i.id !== id);
    updateCartUI();
}

// Global variables for confirmation modal
let confirmAction = null;
let itemToRemove = null;
let orderToCancelId = null;

window.updateCartQty = function(id, change) {
    const item = cart.find(i => i.id === id);
    if (item) {
        if (change < 0 && item.qty === 1) {
            // Show confirmation before removing
            itemToRemove = id;
            confirmAction = 'removeItem';
            document.getElementById('confirm-title').innerText = 'Remove Item?';
            document.getElementById('confirm-message').innerText = `Are you sure you want to remove ${item.name} from your tray?`;
            document.getElementById('confirm-icon').className = 'fas fa-trash text-red-600 dark:text-red-400 text-xl';
            document.getElementById('confirm-modal').classList.remove('hidden');
            document.getElementById('confirm-modal').classList.add('flex');
        } else {
            item.qty += change;
            updateCartUI();
        }
    }
};

window.closeConfirmModal = function() {
    document.getElementById('confirm-modal').classList.add('hidden');
    document.getElementById('confirm-modal').classList.remove('flex');
    confirmAction = null;
    itemToRemove = null;
    orderToCancelId = null;
};

window.proceedConfirm = function() {
    if (confirmAction === 'removeItem' && itemToRemove) {
        removeFromCart(itemToRemove);
        renderMenu(); // Update menu to show + button again
        showToast('Item removed from tray');
    } else if (confirmAction === 'cancelOrder' && orderToCancelId) {
        const order = orders.find(o => o.id === orderToCancelId);
        if(order) {
            order.status = 'cancelled';
            renderHistory();
            showToast('Order Cancelled');
        }
    }
    closeConfirmModal();
};

function updateCartUI() {
    updateSimpleCartUI();
}

window.toggleCart = function() {
    const cartPanel = document.getElementById('cart-panel');
    const backdrop = document.getElementById('cart-backdrop');
    
    const isClosed = cartPanel.classList.contains('translate-x-full');
    
    if (isClosed) {
        cartPanel.classList.remove('translate-x-full');
        backdrop.classList.remove('hidden');
        setTimeout(() => backdrop.classList.remove('opacity-0'), 10); 
    } else {
        cartPanel.classList.add('translate-x-full');
        backdrop.classList.add('opacity-0');
        setTimeout(() => backdrop.classList.add('hidden'), 300); 
    }
};

function placeOrder() {
    // Check if we're on checkout page or using cart
    const checkoutPickup = document.getElementById('checkout-pickup-time');
    const cartPickup = document.getElementById('pickup-time');
    const pickupTime = checkoutPickup && checkoutPickup.value ? checkoutPickup.value : (cartPickup ? cartPickup.value : '');
    
    const checkoutMethod = document.querySelector('input[name="checkout_payment_method"]:checked');
    const cartMethod = document.querySelector('input[name="payment_method"]:checked');
    const paymentMethod = checkoutMethod ? checkoutMethod.value : (cartMethod ? cartMethod.value : 'ewallet');
    
    const checkoutNote = document.getElementById('checkout-note');
    const orderNote = checkoutNote && checkoutNote.value ? checkoutNote.value : '';
    
    const total = cart.reduce((acc, i) => acc + (i.price * i.qty), 0);
    const idStr = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
    
    const newOrder = {
        id: 'M' + idStr,
        studentName: currentUser ? currentUser.name : "Student",
        items: [...cart],
        pickupTime: pickupTime,
        total: total,
        status: 'pending', 
        paymentMethod: paymentMethod,
        note: orderNote,
        timestamp: new Date()
    };

    orders.unshift(newOrder);
    cart = [];
    updateCartUI();
    renderMenu(); // Update menu to reset all items to + button
    
    // Close cart if open
    const cartPanel = document.getElementById('cart-panel');
    if (cartPanel && !cartPanel.classList.contains('translate-x-full')) {
        toggleCart();
    }
    
    // If on checkout page, go back to menu
    const checkoutView = document.getElementById('view-checkout');
    if (checkoutView && !checkoutView.classList.contains('hidden')) {
        switchView('student');
    } else {
        switchView('history');
    }
    
    showToast('Order Placed Successfully! 🎉', false);
}

function loadUserOrders() {
    if(orders.length === 0) {
        orders = [
            { 
                id: 'M' + Math.floor(1000 + Math.random() * 9000), 
                studentName: currentUser ? currentUser.name : 'Student', 
                items: [
                    {id: 4, name: 'Mee Goreng Mamak', price: 4.50, qty: 1},
                    {id: 6, name: 'Fresh Watermelon Juice', price: 3.00, qty: 1}
                ], 
                pickupTime: '12:30 PM', 
                total: 7.50, 
                status: 'pending',
                paymentMethod: 'ewallet',
                timestamp: new Date() 
            },
            { 
                id: 'M892', 
                studentName: currentUser ? currentUser.name : 'Student', 
                items: [{id: 1, name: 'Nasi Lemak Special', price: 5.50, qty: 1}], 
                pickupTime: '12:30 PM', 
                total: 5.50, 
                status: 'completed', 
                timestamp: new Date(Date.now() - 86400000) 
            },
        ];
    }
    renderHistory();
}

function renderHistory() {
    const activeContainer = document.getElementById('active-orders-container');
    const pastContainer = document.getElementById('past-orders-container');
    
    const myOrders = orders.filter(o => o.studentName === (currentUser ? currentUser.name : ''));
    
    const active = myOrders.filter(o => ['pending', 'ready'].includes(o.status));
    const past = myOrders.filter(o => ['completed', 'cancelled'].includes(o.status));

    if(active.length === 0) {
        activeContainer.innerHTML = `<div class="text-gray-400 dark:text-gray-500 text-sm italic">No active orders. Hungry?</div>`;
    } else {
        activeContainer.innerHTML = active.map(order => {
            const isReady = order.status === 'ready';
            return `
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-md p-6 border border-gray-100 dark:border-gray-700 relative overflow-hidden fade-in">
                <div class="absolute top-0 left-0 w-2 h-full ${isReady ? 'bg-green-500' : 'bg-orange-500'}"></div>
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase">Order Number</span>
                        <h3 class="text-4xl font-extrabold text-gray-800 dark:text-white tracking-tighter">${order.id}</h3>
                    </div>
                    <div class="text-right">
                        <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-full text-xs font-bold uppercase">${order.pickupTime}</span>
                    </div>
                </div>
                
                <div class="relative flex justify-between items-center mb-8 px-2">
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 dark:bg-gray-700 -z-10"></div>
                    
                    <div class="progress-step active flex flex-col items-center gap-2 bg-white dark:bg-gray-800 px-2">
                        <div class="circle w-8 h-8 rounded-full border-4 border-green-500 bg-green-500 text-white flex items-center justify-center text-xs"><i class="fas fa-check"></i></div>
                        <span class="text-xs font-bold text-green-600">Placed</span>
                    </div>
                    <div class="progress-step active flex flex-col items-center gap-2 bg-white dark:bg-gray-800 px-2">
                        <div class="circle w-8 h-8 rounded-full border-4 ${isReady ? 'border-green-500 bg-green-500 text-white' : 'border-green-500 bg-white dark:bg-gray-800'} flex items-center justify-center text-xs">
                            ${isReady ? '<i class="fas fa-check"></i>' : '<div class="w-2 h-2 bg-green-500 rounded-full"></div>'}
                        </div>
                        <span class="text-xs font-bold ${isReady ? 'text-green-600' : 'text-gray-800 dark:text-gray-200'}">Prep</span>
                    </div>
                    <div class="progress-step active flex flex-col items-center gap-2 bg-white dark:bg-gray-800 px-2">
                        <div class="circle w-8 h-8 rounded-full border-4 ${isReady ? 'border-green-500 bg-white dark:bg-gray-800 text-green-600' : 'border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800'} flex items-center justify-center text-xs">
                            <i class="fas fa-bag-shopping"></i>
                        </div>
                        <span class="text-xs font-bold ${isReady ? 'text-green-600' : 'text-gray-400'}">Ready</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            ${order.items.map(i => `${i.qty}x ${i.name}`).join(', ')}
                        </div>
                        <div class="font-bold text-gray-800 dark:text-white">${formatCurrency(order.total)}</div>
                    </div>
                    ${order.status === 'pending' ? `
                        <button onclick="event.stopPropagation(); openConfirmModal(event, '${order.id}')" class="mt-4 w-full border border-red-200 dark:border-red-800/50 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-semibold py-2 rounded-lg text-sm transition-colors">
                            Cancel Order
                        </button>
                    ` : ''}
                </div>
            </div>
        `}).join('');
    }

    if(past.length === 0) {
        pastContainer.innerHTML = `<div class="text-gray-400 dark:text-gray-500 text-sm italic">No history found.</div>`;
    } else {
        pastContainer.innerHTML = past.map(order => `
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 fade-in">
                <div class="flex-1 w-full">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="font-bold text-gray-800 dark:text-white">${order.id}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mb-1">${order.items.map(i => `${i.qty}x ${i.name}`).join(', ')}</p>
                    <p class="text-xs text-gray-400">${order.timestamp.toLocaleDateString()}</p>
                </div>
                <div class="flex flex-row sm:flex-col items-center sm:items-end gap-3 w-full sm:w-auto justify-between sm:justify-start">
                    <span class="text-xs font-bold px-3 py-1 rounded-full ${order.status === 'completed' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400'} uppercase tracking-wide">${order.status}</span>
                    <button onclick="reorder('${order.id}')" class="w-auto bg-orange-50 dark:bg-orange-900/30 hover:bg-orange-100 dark:hover:bg-orange-900/50 text-orange-600 dark:text-orange-400 border border-orange-200 dark:border-orange-800 font-bold py-2 px-4 rounded-lg text-sm transition-colors flex items-center gap-2">
                        <i class="fas fa-redo-alt text-xs"></i> Order Again
                    </button>
                </div>
            </div>
        `).join('');
    }
}

window.openConfirmModal = function(event, id) {
    if(event) event.stopPropagation();
    orderToCancelId = id;
    confirmAction = 'cancelOrder';
    
    const order = orders.find(o => o.id === id);
    document.getElementById('confirm-title').innerText = 'Cancel Order?';
    document.getElementById('confirm-message').innerText = order ? `Are you sure you want to cancel order #${order.id}?` : 'Are you sure you want to cancel this order?';
    document.getElementById('confirm-icon').className = 'fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl';
    document.getElementById('confirm-modal').classList.remove('hidden');
    document.getElementById('confirm-modal').classList.add('flex');
};

window.reorder = function(id) {
    const order = orders.find(o => o.id === id);
    if(order) {
        order.items.forEach(item => {
            const existing = cart.find(c => c.id === item.id);
            if(existing) existing.qty += item.qty;
            else cart.push({...item});
        });
        updateCartUI();
        switchView('student');
        const cartPanel = document.getElementById('cart-panel');
        if (cartPanel.classList.contains('translate-x-full')) {
            toggleCart();
        }
        showToast('Items added to tray!');
    }
};

function loadAllOrders() {
    renderAdminOrders();
}

function renderAdminOrders() {
    const tbody = document.getElementById('orders-table-body');
    const noData = document.getElementById('no-orders-msg');
    const pendingCount = document.getElementById('pending-count');

    if (orders.length === 0) {
        tbody.innerHTML = '';
        noData.classList.remove('hidden');
        return;
    }
    
    noData.classList.add('hidden');
    let pending = 0;

    const sortedOrders = [...orders].sort((a, b) => {
        const statusOrder = { 'pending': 1, 'ready': 2, 'completed': 3, 'cancelled': 4 };
        return statusOrder[a.status] - statusOrder[b.status];
    });

    tbody.innerHTML = sortedOrders.map(order => {
        if(order.status === 'pending') pending++;
        
        let statusClass = '';
        let statusText = order.status;
        if(order.status === 'pending') statusClass = 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300';
        else if(order.status === 'ready') statusClass = 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300';
        else if(order.status === 'completed') statusClass = 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300';
        else statusClass = 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300';

        const itemsSummary = order.items.map(i => `${i.qty}x ${i.name}`).join(', ');

        return `
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-750 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0">
            <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">${order.id}</td>
            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">${order.studentName}</td>
            <td class="px-6 py-4 text-gray-600 dark:text-gray-300 truncate max-w-xs" title="${itemsSummary}">${itemsSummary}</td>
            <td class="px-6 py-4 text-gray-600 dark:text-gray-300">${order.pickupTime}</td>
            <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">${formatCurrency(order.total)}</td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-full text-xs font-semibold uppercase ${statusClass}">${statusText}</span>
            </td>
            <td class="px-6 py-4 text-right">
                ${order.status === 'pending' 
                    ? `<button onclick="updateStatus('${order.id}', 'ready')" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg shadow transition">Mark Ready</button>`
                    : order.status === 'ready'
                        ? `<button onclick="updateStatus('${order.id}', 'completed')" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg shadow transition">Complete</button>`
                        : ``
                }
            </td>
        </tr>
        `;
    }).join('');

    pendingCount.innerText = pending;
}

window.updateStatus = function(id, newStatus) {
    const order = orders.find(o => o.id === id);
    if(order) {
        order.status = newStatus;
        renderAdminOrders();
        showToast(`Order ${id} updated`);
    }
};

window.switchView = function(view) {
    const views = ['view-login', 'view-register', 'view-student', 'view-checkout', 'view-history', 'view-admin', 'view-home'];
    const adminBtn = document.getElementById('btn-admin');
    const menuBtn = document.getElementById('nav-menu-btn');
    const historyBtn = document.getElementById('nav-history-btn');
    const homeBtn = document.getElementById('nav-home-btn');
    const fabCart = document.getElementById('fab-cart-btn');

    adminBtn.classList.remove('bg-orange-100', 'dark:bg-orange-900/30', 'text-orange-700', 'dark:text-orange-400');
    adminBtn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
    
    menuBtn.classList.remove('bg-orange-100', 'dark:bg-orange-900/30', 'text-orange-600', 'dark:text-orange-400');
    menuBtn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');

    historyBtn.classList.remove('bg-orange-100', 'dark:bg-orange-900/30', 'text-orange-600', 'dark:text-orange-400');
    historyBtn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');

    homeBtn.classList.remove('bg-orange-100', 'dark:bg-orange-900/30', 'text-orange-600', 'dark:text-orange-400');
    homeBtn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');

    views.forEach(v => {
        const el = document.getElementById(v);
        if(el) {
            el.classList.remove('flex');
            el.classList.add('hidden');
        }
    });

    if (view === 'home' || view === 'student' || view === 'history') {
        fabCart.classList.remove('hidden');
    } else {
        fabCart.classList.add('hidden');
    }

    if (view === 'login') {
        document.getElementById('view-login').classList.remove('hidden');
        document.getElementById('view-login').classList.add('flex');
    } else if (view === 'register') {
        document.getElementById('view-register').classList.remove('hidden');
        document.getElementById('view-register').classList.add('flex');
    } else if (view === 'home') {
        document.getElementById('view-home').classList.remove('hidden');
        document.getElementById('view-home').classList.add('flex');
        renderHome();
        homeBtn.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
        homeBtn.classList.add('bg-orange-100', 'dark:bg-orange-900/30', 'text-orange-600', 'dark:text-orange-400');

    } else if (view === 'student') {
        document.getElementById('view-student').classList.remove('hidden');
        document.getElementById('view-student').classList.add('flex');
        
        menuBtn.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
        menuBtn.classList.add('bg-orange-100', 'dark:bg-orange-900/30', 'text-orange-600', 'dark:text-orange-400');

    } else if (view === 'checkout') {
        document.getElementById('view-checkout').classList.remove('hidden');
        document.getElementById('view-checkout').classList.add('flex');
        updateCheckoutUI();

    } else if (view === 'history') {
        document.getElementById('view-history').classList.remove('hidden');
        document.getElementById('view-history').classList.add('flex');
        renderHistory();
        historyBtn.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
        historyBtn.classList.add('bg-orange-100', 'dark:bg-orange-900/30', 'text-orange-600', 'dark:text-orange-400');

    } else if (view === 'admin') {
        document.getElementById('view-admin').classList.remove('hidden');
        document.getElementById('view-admin').classList.add('flex');
        renderAdminOrders();
        adminBtn.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-100', 'dark:hover:bg-gray-700');
        adminBtn.classList.add('bg-orange-100', 'dark:bg-orange-900/30', 'text-orange-700', 'dark:text-orange-400');
    }
};

// --- Simplified Cart Functions ---
window.toggleCartEditMode = function() {
    isCartEditMode = !isCartEditMode;
    const editBtn = document.getElementById('cart-edit-btn');
    
    if (isCartEditMode) {
        editBtn.innerHTML = '<i class="fas fa-check mr-1"></i>Done';
        editBtn.classList.add('text-green-600', 'dark:text-green-400');
        editBtn.classList.remove('text-orange-600', 'dark:text-orange-400');
    } else {
        editBtn.innerHTML = '<i class="fas fa-pen mr-1"></i>Edit';
        editBtn.classList.remove('text-green-600', 'dark:text-green-400');
        editBtn.classList.add('text-orange-600', 'dark:text-orange-400');
        selectedItems = []; // Clear selections when exiting edit mode
    }
    updateSimpleCartUI();
};

window.selectAllCartItems = function() {
    selectedItems = cart.map(item => item.id);
    updateSimpleCartUI();
};

window.deselectAllCartItems = function() {
    selectedItems = [];
    updateSimpleCartUI();
};

function updateSimpleCartUI() {
    const container = document.getElementById('cart-items-simple');
    const totalSpan = document.getElementById('cart-simple-total');
    const checkoutBtn = document.getElementById('btn-cart-checkout');
    const mobileCount = document.getElementById('mobile-cart-count');
    const editBtn = document.getElementById('cart-edit-btn');
    
    const totalQty = cart.reduce((acc, i) => acc + i.qty, 0);
    if(totalQty > 0) {
        mobileCount.innerText = totalQty;
        mobileCount.classList.remove('hidden');
        if(editBtn) editBtn.classList.remove('hidden');
    } else {
        mobileCount.classList.add('hidden');
        if(editBtn) editBtn.classList.add('hidden');
        isCartEditMode = false;
    }

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center text-gray-400 dark:text-gray-500 py-12">
                <i class="fas fa-basket-shopping text-4xl mb-3"></i>
                <p class="font-medium">Your tray is empty</p>
                <p class="text-sm mt-1">Add items to get started</p>
            </div>`;
        checkoutBtn.disabled = true;
        totalSpan.innerText = formatCurrency(0);
        return;
    }

    let total = 0;
    
    if (isCartEditMode) {
        const allSelected = cart.length > 0 && selectedItems.length === cart.length;
        container.innerHTML = `
            <div class="flex items-center justify-between mb-3 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <button onclick="${allSelected ? 'deselectAllCartItems()' : 'selectAllCartItems()'}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                    <i class="fas fa-${allSelected ? 'times-circle' : 'check-circle'} mr-1"></i>
                    ${allSelected ? 'Deselect All' : 'Select All'}
                </button>
                <span class="text-xs text-gray-600 dark:text-gray-400">${selectedItems.length} of ${cart.length} selected</span>
            </div>
        ` + cart.map(item => {
            total += item.price * item.qty;
            const isSelected = selectedItems.includes(item.id);
            return `
            <label class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors ${isSelected ? 'ring-2 ring-red-500' : ''}">
                <input type="checkbox" ${isSelected ? 'checked' : ''} onchange="toggleItemSelection(${item.id})" class="w-5 h-5 text-red-600 focus:ring-red-500 rounded">
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-100">${item.name}</h4>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">${item.qty}x</span>
                        <span class="text-sm font-bold text-orange-600 dark:text-orange-400">${formatCurrency(item.price)}</span>
                    </div>
                </div>
                <div class="text-sm font-bold text-gray-800 dark:text-white">
                    ${formatCurrency(item.price * item.qty)}
                </div>
            </label>`;
        }).join('') + (selectedItems.length > 0 ? `
            <button onclick="removeSelectedItems()" class="w-full mt-3 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-trash"></i>
                <span>Remove ${selectedItems.length} Item(s)</span>
            </button>
        ` : '');
    } else {
        container.innerHTML = cart.map(item => {
            total += item.price * item.qty;
            return `
            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-100">${item.name}</h4>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">${item.qty}x</span>
                        <span class="text-sm font-bold text-orange-600 dark:text-orange-400">${formatCurrency(item.price)}</span>
                    </div>
                </div>
                <div class="text-sm font-bold text-gray-800 dark:text-white">
                    ${formatCurrency(item.price * item.qty)}
                </div>
            </div>`;
        }).join('');
    }

    totalSpan.innerText = formatCurrency(total);
    checkoutBtn.disabled = false;
}

window.goToCheckout = function() {
    if (cart.length === 0) return;
    toggleCart();
    switchView('checkout');
};

// --- Checkout Page Functions ---
window.toggleEditMode = function() {
    isEditMode = !isEditMode;
    const editBtn = document.getElementById('edit-cart-btn');
    
    if (isEditMode) {
        editBtn.innerHTML = '<i class="fas fa-check mr-1"></i>Done';
        editBtn.classList.add('text-green-600', 'dark:text-green-400');
        editBtn.classList.remove('text-orange-600', 'dark:text-orange-400');
    } else {
        editBtn.innerHTML = '<i class="fas fa-pen mr-1"></i>Edit';
        editBtn.classList.remove('text-green-600', 'dark:text-green-400');
        editBtn.classList.add('text-orange-600', 'dark:text-orange-400');
    }
    updateCheckoutUI();
};

let selectedItems = [];

window.toggleItemSelection = function(id) {
    const index = selectedItems.indexOf(id);
    if (index > -1) {
        selectedItems.splice(index, 1);
    } else {
        selectedItems.push(id);
    }
    // Update both cart and checkout UI
    if (isCartEditMode) {
        updateSimpleCartUI();
    }
    if (isEditMode) {
        updateCheckoutUI();
    }
};

window.removeSelectedItems = function() {
    if (selectedItems.length === 0) return;
    
    const count = selectedItems.length;
    selectedItems.forEach(id => {
        cart = cart.filter(item => item.id !== id);
    });
    
    selectedItems = [];
    
    // Exit edit mode in both cart and checkout
    isEditMode = false;
    isCartEditMode = false;
    
    const editBtn = document.getElementById('edit-cart-btn');
    if (editBtn) {
        editBtn.innerHTML = '<i class="fas fa-pen mr-1"></i>Edit';
        editBtn.classList.remove('text-green-600', 'dark:text-green-400');
        editBtn.classList.add('text-orange-600', 'dark:text-orange-400');
    }
    
    const cartEditBtn = document.getElementById('cart-edit-btn');
    if (cartEditBtn) {
        cartEditBtn.innerHTML = '<i class="fas fa-pen mr-1"></i>Edit';
        cartEditBtn.classList.remove('text-green-600', 'dark:text-green-400');
        cartEditBtn.classList.add('text-orange-600', 'dark:text-orange-400');
    }
    
    updateCheckoutUI();
    updateSimpleCartUI();
    renderMenu();
    showToast(`${count} item(s) removed`);
};

function updateCheckoutUI() {
    const container = document.getElementById('checkout-items');
    const subtotalSpan = document.getElementById('checkout-subtotal');
    const totalSpan = document.getElementById('checkout-total');
    const placeOrderBtn = document.getElementById('checkout-place-order-btn');
    const pickupSelect = document.getElementById('checkout-pickup-time');
    
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center text-gray-400 dark:text-gray-500 py-8">
                <i class="fas fa-basket-shopping text-3xl mb-2"></i>
                <p class="font-medium">No items in cart</p>
            </div>`;
        placeOrderBtn.disabled = true;
        subtotalSpan.innerText = formatCurrency(0);
        totalSpan.innerText = formatCurrency(0);
        return;
    }

    let total = 0;
    
    container.innerHTML = cart.map(item => {
        total += item.price * item.qty;
        return `
        <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
            <div class="flex-1">
                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-100">${item.name}</h4>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-gray-500 dark:text-gray-400">${item.qty}x ${formatCurrency(item.price)}</span>
                </div>
            </div>
            <div class="text-sm font-bold text-gray-800 dark:text-white">
                ${formatCurrency(item.price * item.qty)}
            </div>
        </div>`;
    }).join('');

    subtotalSpan.innerText = formatCurrency(total);
    totalSpan.innerText = formatCurrency(total);
    placeOrderBtn.disabled = false;
    
    // Ensure pickup times are generated
    if (pickupSelect && pickupSelect.options.length === 0) {
        generatePickupTimes();
    }
}
