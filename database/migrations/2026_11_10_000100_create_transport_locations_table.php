<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransportLocationsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('transport_locations')) {
            Schema::create('transport_locations', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('name', 150);
                $table->string('city', 100);
                $table->enum('type', ['airport', 'train', 'bus', 'port'])->default('airport');
                $table->string('country', 100)->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('type');
                $table->index('city');
                $table->index('is_active');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('transport_locations');
    }
}
