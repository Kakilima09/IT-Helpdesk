<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGaRequestItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ga_request_items')) {
            Schema::create('ga_request_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('ga_request_id');
                $table->foreign('ga_request_id')->references('id')->on('ga_requests')->onDelete('cascade');

                $table->enum('item_type', ['goods', 'service'])->default('goods');
                $table->unsignedBigInteger('category_id')->nullable();
                $table->foreign('category_id')->references('id')->on('ga_categories')->onDelete('set null');
                $table->string('name');
                $table->integer('qty')->default(1);
                $table->string('unit')->nullable();
                $table->decimal('price', 15, 2)->default(0);
                $table->decimal('amount', 15, 2)->default(0);

                $table->timestamps();

                $table->index('ga_request_id');
                $table->index('category_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ga_request_items');
    }
}