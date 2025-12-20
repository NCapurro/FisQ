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
        Schema::create('activity_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // Quién lo hizo
        $table->string('action'); // Ej: 'crear', 'eliminar', 'restaurar'
        $table->string('module'); // Ej: 'Escuelas', 'Usuarios'
        $table->text('description'); // Ej: 'Se eliminó la escuela Normal 5'
        $table->timestamps(); // Cuándo lo hizo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
