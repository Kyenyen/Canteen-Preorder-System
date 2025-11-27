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
        Schema::create('orders', function (Blueprint $table) {
            $table->string('order_id', 5)->primary();
            $table->string('user_id', 5);
            $table->string('status', 15);
            $table->decimal('total', 6, 2);
            $table->date('date');
            $table->time('pickup_time');
            $table->string('dining_option', 15);

            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });

        DB::statement("ALTER TABLE orders ADD CONSTRAINT chk_status CHECK (status IN ('Preparing', 'Refunded', 'Ready', 'Completed'));");
        DB::statement("ALTER TABLE orders ADD CONSTRAINT chk_dining_option CHECK (dining_option IN ('Dine-in', 'Takeaway'));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
