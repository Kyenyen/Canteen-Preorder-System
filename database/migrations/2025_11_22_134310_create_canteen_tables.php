<?php

// database/migrations/xxxx_xx_xx_create_canteen_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Update Users Table (Add 'role')
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'admin'])->default('student')->after('email');
        });

        // 2. Menu Items Table
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('category'); // e.g., 'Breakfast', 'Lunch', 'Drink'
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        // 3. Orders Table
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pickup_time'); 
            $table->decimal('total_amount', 8, 2);
            // Payment Info
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->string('payment_method')->nullable(); // e.g., 'ewallet', 'card', 'duitnow'
            // Order Status
            $table->enum('status', ['pending', 'ready', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });

        // 4. Order Items Pivot Table
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('menu_id')->constrained();
            $table->integer('quantity');
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('menus');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};

