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
        Schema::create('users', function (Blueprint $table) {
            $table->string('UserId', 5)->primary();
            $table->string('Username', 30);
            $table->string('Email', 30)->unique();
            $table->string('Role', 30);
            $table->string('Password', 255);
            $table->string('Photo', 100)->nullable();
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_role CHECK (Role IN ('User', 'Admin'));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
