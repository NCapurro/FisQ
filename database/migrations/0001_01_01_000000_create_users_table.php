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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

             // ID & Login
            $table->string('dni', 8)->unique();
            $table->string('name', 50);
            $table->string('lastname', 50);
            $table->string('email', 50)->unique();
            $table->string('password');
    
            // Contact Info
            $table->string('phone', 15)->nullable();
            $table->string('address', 100)->nullable();
            
            // AQUÍ EL CAMBIO CLAVE:
            
            // Role: admin, user
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->foreignId('department_id')->constrained()->onDelete('cascade'); 

            // Metadata
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
