<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddApplicantTypeToPapdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('jenis_pemohon', 20)->default('internal')->after('user_id');
            $table->string('id_karyawan')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::table('papd_requests')->whereNull('id_karyawan')->exists()) {
            throw new \RuntimeException('Cannot roll back while external PAPD requests have no employee ID.');
        }

        Schema::table('papd_requests', function (Blueprint $table) {
            $table->string('id_karyawan')->nullable(false)->change();
            $table->dropColumn('jenis_pemohon');
        });
    }
}
