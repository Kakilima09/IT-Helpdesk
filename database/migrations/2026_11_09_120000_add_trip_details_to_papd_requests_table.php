<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTripDetailsToPapdRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('nama_sesuai_ktp')->nullable()->after('jenis_perjalanan');
            $table->string('role_perjalanan', 20)->default('sendiri')->after('agenda');
            $table->text('nama_anggota_team')->nullable()->after('role_perjalanan');
            $table->unsignedInteger('jumlah_anggota_team')->nullable()->after('nama_anggota_team');
        });
    }

    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropColumn([
                'nama_sesuai_ktp',
                'role_perjalanan',
                'nama_anggota_team',
                'jumlah_anggota_team',
            ]);
        });
    }
}
