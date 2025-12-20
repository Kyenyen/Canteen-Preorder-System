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
            $table->string('user_id', 5)->primary();
            $table->string('username', 30);
            $table->string('email', 100)->unique();
            $table->string('role', 30);
            $table->string('password', 255);
            $table->string('photo', 100)->nullable();
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_role CHECK (role IN ('user', 'admin'));");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
