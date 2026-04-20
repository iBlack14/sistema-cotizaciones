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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->text('content');
            $table->enum('type', ['email', 'whatsapp', 'sms', 'notification'])->default('email');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['draft', 'sent', 'delivered', 'failed', 'pending'])->default('draft');
            $table->json('recipients')->nullable(); // Para almacenar múltiples destinatarios
            $table->string('recipient_type')->nullable(); // 'domains', 'quotations', 'custom'
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->json('metadata')->nullable(); // Para datos adicionales como archivos adjuntos
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};