<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if custom_messages table exists
        if (Schema::hasTable('custom_messages')) {
            $customMessages = DB::table('custom_messages')->get();

            // Get current max number
            $currentMaxNumber = DB::table('predefined_messages')->max('number') ?? 100;

            foreach ($customMessages as $message) {
                $currentMaxNumber++;

                // Check if this message already exists (by content or title) to avoid duplicates
                $exists = DB::table('predefined_messages')
                    ->where('content', $message->content)
                    ->orWhere('title', $message->name)
                    ->exists();

                if (!$exists) {
                    DB::table('predefined_messages')->insert([
                        'number' => $currentMaxNumber,
                        'title' => $message->name,
                        'content' => $message->content,
                        'type' => $message->type === 'both' ? 'whatsapp' : $message->type,
                        'is_active' => true,
                        'is_favorite' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to automatically delete messages in down() as they might have been edited.
        // This is a data migration.
    }
};
