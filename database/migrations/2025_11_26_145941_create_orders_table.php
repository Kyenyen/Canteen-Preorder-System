<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->string('OrderId', 5)->primary();
            $table->string('UserId', 5);
            $table->string('Status', 15);
            $table->decimal('Total', 6, 2);
            $table->date('Date');
            $table->time('PickupTime');
            $table->string('DiningOption', 15);

            $table->foreign('UserId')->references('UserId')->on('users')->onDelete('cascade');
            $table->check("Status IN ('Preparing', 'Refunded', 'Ready', 'Completed')");
            $table->check("DiningOption IN ('Dine-in', 'Takeaway')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
