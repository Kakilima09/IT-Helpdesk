<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationPrefToUsersAndCustomersTables extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'notification_pref')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('notification_pref', ['email', 'whatsapp', 'both'])->default('email')->after('darkmode');
            });
        }

        if (Schema::hasTable('customers') && !Schema::hasColumn('customers', 'notification_pref')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->enum('notification_pref', ['email', 'whatsapp', 'both'])->default('email');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'notification_pref')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('notification_pref');
            });
        }

        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'notification_pref')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('notification_pref');
            });
        }
    }
}