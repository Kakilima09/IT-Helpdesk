<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClosingStatusToPapdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            // Tambahkan kolom closing_status default 'pending'
            $table->enum('closing_status', ['pending', 'done', 'cancel'])
                  ->default('pending')->after('status');
            $table->timestamp('closed_at')->nullable()->after('closing_status');
            $table->text('closing_note')->nullable()->after('closed_at');
        });

        // Untuk status 'expired' jika belum ada, tambahkan juga
        // Jika sebelumnya sudah ditambahkan, abaikan
        DB::statement("ALTER TABLE papd_requests MODIFY status ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropColumn(['closing_status', 'closed_at', 'closing_note']);
        });
    }
}
