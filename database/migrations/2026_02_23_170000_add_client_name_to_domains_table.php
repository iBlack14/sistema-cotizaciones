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
        DB::statement('
            UPDATE domains d
            INNER JOIN users u ON u.id = d.user_id
            SET d.client_name = u.name
            WHERE d.client_name IS NULL
        ');
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

