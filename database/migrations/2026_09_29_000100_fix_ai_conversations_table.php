<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixAiConversationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ai_conversations')) {
            return;
        }

        // 1. Kolom ticket_id belum ada di database meskipun ada di migration awal.
        if (!Schema::hasColumn('ai_conversations', 'ticket_id')) {
            Schema::table('ai_conversations', function (Blueprint $table) {
                $table->unsignedBigInteger('ticket_id')->nullable()->after('status');
            });
        }

        if (!Schema::hasColumn('ai_conversations', 'title')) {
            Schema::table('ai_conversations', function (Blueprint $table) {
                $table->string('title')->nullable()->after('cust_id');
            });
        }

        if (!Schema::hasColumn('ai_conversations', 'resolved_at')) {
            Schema::table('ai_conversations', function (Blueprint $table) {
                $table->timestamp('resolved_at')->nullable()->after('ticket_id');
            });
        }

        // 2. Foreign key awal nunjuk ke users, padahal cust_id merujuk customers.
        $this->dropForeignKey('ai_conversations', 'ai_conversations_cust_id_foreign');

        $hasCustomersFk = DB::select("SELECT COUNT(*) AS c FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'ai_conversations'
              AND COLUMN_NAME = 'cust_id' AND REFERENCED_TABLE_NAME = 'customers'");

        if (empty($hasCustomersFk) || ! $hasCustomersFk[0]->c) {
            try {
                DB::statement('ALTER TABLE ai_conversations
                    ADD CONSTRAINT ai_conversations_cust_id_foreign
                    FOREIGN KEY (cust_id) REFERENCES customers(id) ON DELETE CASCADE');
            } catch (\Throwable $e) {
                // Abaikan bila tabel customers belum ada / constraint bentrok.
            }
        }
    }

    public function down()
    {
        $this->dropForeignKey('ai_conversations', 'ai_conversations_cust_id_foreign');
    }

    protected function dropForeignKey($table, $name)
    {
        try {
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$name}");
        } catch (\Throwable $e) {
            // Constraint tidak ada, tidak perlu dilepas.
        }
    }
}
