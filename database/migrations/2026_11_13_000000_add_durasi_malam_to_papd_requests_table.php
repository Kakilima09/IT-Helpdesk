<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDurasiMalamToPapdRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->unsignedInteger('durasi_malam')->default(0)->after('durasi_hari');
        });

        DB::table('papd_requests')
            ->whereNotNull('tanggal_kepulangan')
            ->update([
                'durasi_hari' => DB::raw('DATEDIFF(tanggal_kepulangan, tanggal_keberangkatan) + 1'),
                'durasi_malam' => DB::raw('DATEDIFF(tanggal_kepulangan, tanggal_keberangkatan)'),
            ]);
    }

    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropColumn('durasi_malam');
        });
    }
}
