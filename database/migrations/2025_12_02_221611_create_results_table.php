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
        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->integer('votes')->unsigned();
            $table->foreignId('user_id')->constrained();
            
            $table->foreignId('mesa_id')->constrained()->onDelete('cascade');
            $table->foreignId('political_party_id')->constrained()->onDelete('cascade');
            
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            $table->unique(['mesa_id', 'political_party_id']);
            $table->timestamps();
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
