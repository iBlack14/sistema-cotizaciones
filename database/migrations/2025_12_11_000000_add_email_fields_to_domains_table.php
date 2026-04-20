<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailFieldsToDomainsTable extends Migration
{
    public function up()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->text('corporate_emails')->nullable()->after('maintenance_status');
            $table->text('emails')->nullable()->after('corporate_emails');
        });
    }

    public function down()
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['corporate_emails', 'emails']);
        });
    }
}
