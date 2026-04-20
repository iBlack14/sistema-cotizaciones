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
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('domain_name')->unique();
            $table->date('registration_date');
            $table->date('expiration_date');
            $table->boolean('auto_renew')->default(false);
            $table->enum('status', ['activo', 'expirado', 'pendiente', 'suspendido'])->default('activo');
            $table->decimal('price', 10, 2)->default(0);
            $table->text('hosting_info')->nullable();
            $table->text('dns_servers')->nullable();
            $table->text('notes')->nullable();
            $table->text('plugins')->nullable(); // Lista de plugins instalados
            $table->text('licenses')->nullable(); // Licencias activas (ej: Antivirus activo, SSL activo)
            $table->string('maintenance_status')->default('activo'); // activo o inactivo
            $table->timestamps();

            $table->index('status');
            $table->index('expiration_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
