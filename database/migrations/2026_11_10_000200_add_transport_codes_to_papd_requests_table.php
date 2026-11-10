<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransportCodesToPapdRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('kota_asal', 255)->nullable()->after('kota_tujuan');
            $table->string('kode_asal', 10)->nullable()->after('kota_asal');
            $table->string('kode_tujuan', 10)->nullable()->after('kode_asal');

            $table->index('kode_asal');
            $table->index('kode_tujuan');
        });
    }

    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropIndex(['kode_asal']);
            $table->dropIndex(['kode_tujuan']);
            $table->dropColumn(['kota_asal', 'kode_asal', 'kode_tujuan']);
        });
    }
}
