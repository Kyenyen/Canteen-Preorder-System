# Canteen Preorder System

A web-based Canteen Preorder System built with **Laravel** (Backend) and **Vue.js** (Frontend). This system allows users to preorder food items, integrating **Stripe** for payments and **Google Sign-In** for authentication.

## 🛠 Tech Stack
- **Framework:** Laravel
- **Frontend:** Vue.js 3 + Pinia + Vue Router
- **Styling:** Tailwind CSS
- **Database:** MySQL (via XAMPP)
- **Payment Gateway:** Stripe
- **PDF Generation:** DomPDF

---

## 📋 Prerequisites

Before running the project, ensure you have the following software installed from their official websites:

1.  **XAMPP** (for MySQL Database)
2.  **Node.js** (v24.11.1-x64)
3.  **Composer**
4.  **Stripe CLI** (stripe_1.33.0_windows_x86_64)

---

## ⚙️ Installation Guide

Follow these steps to set up the project locally.

## 1. Backend Setup (Laravel)

Open your terminal in the project root folder and run the following commands:

### Install PHP dependencies
composer install  

### Install required packages for PDF and Payments
composer require dompdf/dompdf  
composer require stripe/stripe-php  

### Create the environment file
cp .env.example .env  

### Generate Application Key
php artisan key:generate  

### Link the storage folder
php artisan storage:link  


## 2. Frontend Setup (Vue.js & Tailwind)
### Core Vue dependencies
npm install vue@3  
npm install pinia  
npm install vue-router@4  
npm install vue@3 @vitejs/plugin-vue --save-dev  

### UI & Utilities
npm install chart.js  
npm install vue3-google-signin  
npm install vue-recaptcha  
npm install @stripe/stripe-js  

### Tailwind CSS Setup
npm install -D tailwindcss@3.4.1 postcss autoprefixer  
node node_modules/tailwindcss/lib/cli.js init -p  


## 3. Database Configuration
1. Open XAMPP Control Panel and start Apache and MySQL.
2. Create a new database in phpMyAdmin (usually named canteen_system or similar—check your .env file DB_DATABASE setting).
3. Run the migrations and seeders:

### Run database migrations
php artisan migrate  

### Seed the admin user
php artisan db:seed --class=AdminUserSeeder  

### (Optional) If you need to reset the database completely later:
php artisan migrate:fresh  

# 🚀 How to Run
You need to run the backend and frontend terminals simultaneously.

## Terminal 1 (Backend):
php artisan serve  

## Terminal 2 (Frontend):
npm run dev  

After these, the application should now be accessible at http://127.0.0.1:8000 (or the URL provided in your terminal 1).

## 🔑 Key Configuration (.env)
Ensure your .env file has the following keys configured (obtained from your Stripe and Google Console):

STRIPE_KEY=your_stripe_public_key
STRIPE_SECRET=your_stripe_secret_key

GOOGLE_CLIENT_ID=your_google_client_id
