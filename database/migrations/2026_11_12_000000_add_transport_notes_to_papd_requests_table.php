<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransportNotesToPapdRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->text('catatan_berangkat')->nullable()->after('rute');
            $table->text('catatan_pulang')->nullable()->after('rute_pulang');
        });
    }

    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropColumn(['catatan_berangkat', 'catatan_pulang']);
        });
    }
}
