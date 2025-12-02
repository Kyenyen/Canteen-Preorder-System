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
        // 1. Create the new categories table
        Schema::create('categories', function (Blueprint $table) {
            // Using a char(5) as the primary key as requested
            // This assumes the category IDs are short, fixed-length alphanumeric codes
            $table->char('category_id', 5)->primary();
            
            // Name of the category, max length 100
            $table->string('name', 100)->unique(); 
            
            $table->unsignedInteger('quantity')->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};