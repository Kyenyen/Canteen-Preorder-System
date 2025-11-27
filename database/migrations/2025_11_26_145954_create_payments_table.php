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
        Schema::create('payments', function (Blueprint $table) {
            $table->string('PaymentId', 5)->primary();
            $table->string('OrderId', 5);
            $table->string('Method', 30);
            $table->boolean('Refunded')->default(false);
            $table->date('RefundDate')->nullable();

            $table->foreign('OrderId')->references('OrderId')->on('orders')->onDelete('cascade');
            $table->check("Method IN ('Credit', 'Debit', 'QR')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
