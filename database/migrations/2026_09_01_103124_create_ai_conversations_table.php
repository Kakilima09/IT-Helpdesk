<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAiConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cust_id'); // atau 'cust_id'
            $table->string('session_id')->nullable();
            $table->enum('status', ['ongoing', 'resolved', 'escalated'])->default('ongoing');
            $table->unsignedBigInteger('ticket_id')->nullable();
            $table->timestamps();
        
            $table->foreign('cust_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ai_conversations');
    }
}
