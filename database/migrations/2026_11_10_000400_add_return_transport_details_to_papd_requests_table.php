<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReturnTransportDetailsToPapdRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('kelas_pulang', 50)->nullable()->after('moda_transportasi_pulang');
            $table->string('kota_asal_pulang', 255)->nullable()->after('kelas_pulang');
            $table->string('kode_asal_pulang', 10)->nullable()->after('kota_asal_pulang');
            $table->string('kode_tujuan_pulang', 10)->nullable()->after('kode_asal_pulang');
            $table->string('rute_pulang', 255)->nullable()->after('kode_tujuan_pulang');
            $table->string('detail_maskapai_pulang', 255)->nullable()->after('rute_pulang');
            $table->string('no_penerbangan_pulang', 50)->nullable()->after('detail_maskapai_pulang');
            $table->boolean('bagasi_tambahan_pulang')->nullable()->after('no_penerbangan_pulang');
            $table->boolean('transportasi_lokal_pulang')->nullable()->after('bagasi_tambahan_pulang');
            $table->json('opsi_transportasi_pulang')->nullable()->after('transportasi_lokal_pulang');

            $table->index('kode_asal_pulang');
            $table->index('kode_tujuan_pulang');
        });
    }

    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropIndex(['kode_asal_pulang']);
            $table->dropIndex(['kode_tujuan_pulang']);
            $table->dropColumn([
                'kelas_pulang',
                'kota_asal_pulang',
                'kode_asal_pulang',
                'kode_tujuan_pulang',
                'rute_pulang',
                'detail_maskapai_pulang',
                'no_penerbangan_pulang',
                'bagasi_tambahan_pulang',
                'transportasi_lokal_pulang',
                'opsi_transportasi_pulang',
            ]);
        });
    }
}
