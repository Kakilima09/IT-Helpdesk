<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTicketTypeToPapdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('jenis_tiket', 20)->default('sekali_jalan')->after('moda_transportasi');
        });

        DB::table('papd_requests')
            ->whereNotNull('tanggal_kepulangan')
            ->update(['jenis_tiket' => 'pulang_pergi']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropColumn('jenis_tiket');
        });
    }
}
