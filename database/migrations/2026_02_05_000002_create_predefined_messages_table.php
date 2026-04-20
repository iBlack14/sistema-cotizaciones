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
        Schema::create('predefined_messages', function (Blueprint $table) {
            $table->id();
            $table->integer('number'); // Número del mensaje predeterminado
            $table->string('title'); // Título del mensaje
            $table->text('content'); // Contenido del mensaje
            $table->enum('type', ['email', 'whatsapp', 'both'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0); // Para ordenar los mensajes
            $table->timestamps();
            
            $table->unique('number');
            $table->index(['type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predefined_messages');
    }
};