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
        Schema::create('products', function (Blueprint $table) {
            $table->string('ProductId', 5)->primary();
            $table->string('Name', 100);
            $table->decimal('Price', 6, 2);
            $table->string('Photo', 100)->nullable();
            $table->string('Description', 1000);
            $table->boolean('IsAvailable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
