<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Quotation;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('quotations', 'slug')) {
            Schema::table('quotations', function (Blueprint $table) {
                $table->string('slug')->unique()->nullable()->after('total');
            });

            // Generate slugs for existing quotations
            Quotation::all()->each(function ($quotation) {
                $serviceName = $quotation->items->first()->service_name ?? 'servicio';
                $slugBase = Str::slug($serviceName . '-' . $quotation->id);
                $quotation->slug = $slugBase . '-' . Str::random(5);
                $quotation->save();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
