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
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->unique();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['created', 'asigned', 'scrutinized'])->default('created');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_path')->nullable();
            $table->timestamps();
            $table->boolean('is_active')->default(true);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
