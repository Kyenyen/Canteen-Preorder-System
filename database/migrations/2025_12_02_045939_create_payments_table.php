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
        Schema::create('payments', function (Blueprint $table) {
            $table->string('payment_id', 5)->primary();
            $table->string('order_id', 5);
            $table->string('method', 30);
            $table->boolean('refunded')->default(false);
            $table->date('refund_date')->nullable();
            $table->foreign('order_id')->references('order_id')->on('orders')->onDelete('cascade');
        });

        DB::statement("ALTER TABLE payments ADD CONSTRAINT chk_method CHECK (method IN ('Credit', 'Debit', 'QR'));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
