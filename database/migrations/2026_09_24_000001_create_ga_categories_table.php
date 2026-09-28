<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGaCategoriesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ga_categories')) {
            Schema::create('ga_categories', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique();
                $table->string('name');
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ga_categories');
    }
}