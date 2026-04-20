<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->string('client_name')->nullable()->after('user_id');
            $table->index('client_name');
        });

        // Backfill with existing user names so old records keep client labels.
        DB::table('domains')
            ->whereNull('client_name')
            ->update([
                'client_name' => DB::raw(
                    '(SELECT name FROM users WHERE users.id = domains.user_id LIMIT 1)'
                ),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropIndex(['client_name']);
            $table->dropColumn('client_name');
        });
    }
};
