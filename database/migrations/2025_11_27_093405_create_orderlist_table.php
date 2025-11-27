<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orderlist', function (Blueprint $table) {
            $table->string('OrderId', 5);
            $table->string('ProductId', 5);
            $table->integer('Quantity');
            $table->decimal('Subtotal', 6, 2);

            $table->primary(['OrderId', 'ProductId']);
            $table->foreign('OrderId')->references('OrderId')->on('orders')->onDelete('cascade');
            $table->foreign('ProductId')->references('ProductId')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_lists');
    }
};
