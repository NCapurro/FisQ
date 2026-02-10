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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Quién reporta (Fiscal)
            $table->foreignId('mesa_id')->nullable()->constrained(); // Dónde (opcional)
            $table->text('description'); // "Faltan boletas", "Se cortó la luz"
            $table->enum('priority', ['baja', 'media', 'alta'])->default('media');
            $table->boolean('is_resolved')->default(false);
            $table->timestamps(); // Created_at te da la hora exacta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
