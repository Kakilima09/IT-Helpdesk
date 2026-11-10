<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddReturnTransportModeToPapdRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('moda_transportasi_pulang', 20)->nullable()->after('moda_transportasi');
        });

        DB::table('papd_requests')
            ->whereNotNull('tanggal_kepulangan')
            ->update(['moda_transportasi_pulang' => DB::raw('moda_transportasi')]);
    }

    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropColumn('moda_transportasi_pulang');
        });
    }
}
