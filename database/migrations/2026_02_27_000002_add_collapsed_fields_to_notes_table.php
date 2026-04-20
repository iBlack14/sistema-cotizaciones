<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->boolean('collapsed')->default(false)->after('descripcion');
            $table->string('pin_side', 10)->default('right')->after('collapsed');
        });
    }

    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn(['collapsed', 'pin_side']);
        });
    }
};
