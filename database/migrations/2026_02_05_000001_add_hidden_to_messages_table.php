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
        Schema::table('messages', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('metadata');
            $table->timestamp('hidden_at')->nullable()->after('is_hidden');
            $table->integer('message_number')->nullable()->after('hidden_at');

            $table->index(['user_id', 'is_hidden', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_hidden', 'created_at']);
            $table->dropColumn(['is_hidden', 'hidden_at', 'message_number']);
        });
    }
};
