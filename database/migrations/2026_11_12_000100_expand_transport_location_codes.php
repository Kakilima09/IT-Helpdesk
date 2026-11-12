<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExpandTransportLocationCodes extends Migration
{
    public function up()
    {
        Schema::table('transport_locations', function (Blueprint $table) {
            $table->string('code', 32)->change();
        });

        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('kode_asal', 32)->nullable()->change();
            $table->string('kode_tujuan', 32)->nullable()->change();
            $table->string('kode_asal_pulang', 32)->nullable()->change();
            $table->string('kode_tujuan_pulang', 32)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('kode_asal', 10)->nullable()->change();
            $table->string('kode_tujuan', 10)->nullable()->change();
            $table->string('kode_asal_pulang', 10)->nullable()->change();
            $table->string('kode_tujuan_pulang', 10)->nullable()->change();
        });

        Schema::table('transport_locations', function (Blueprint $table) {
            $table->string('code', 10)->change();
        });
    }
}
