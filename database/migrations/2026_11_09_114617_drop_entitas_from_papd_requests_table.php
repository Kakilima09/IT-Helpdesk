<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropEntitasFromPapdRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('papd_requests', 'entitas')) {
            Schema::table('papd_requests', function (Blueprint $table) {
                $table->dropColumn('entitas');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasColumn('papd_requests', 'entitas')) {
            Schema::table('papd_requests', function (Blueprint $table) {
                $table->string('entitas')->nullable();
            });
        }
    }
}
