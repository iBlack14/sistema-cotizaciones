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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('service_mappings');
        Schema::dropIfExists('service_images');
        Schema::enableForeignKeyConstraints();

        Schema::create('service_images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('filename');
            $table->string('path');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('service_mappings', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->unsignedBigInteger('service_image_id');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('service_image_id')
                ->references('id')
                ->on('service_images')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('service_mappings');
        Schema::dropIfExists('service_images');
        Schema::enableForeignKeyConstraints();
    }
};
