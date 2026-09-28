<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGaRequestsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ga_requests')) {
            Schema::create('ga_requests', function (Blueprint $table) {
                $table->id();
                $table->string('request_no')->unique();
                $table->enum('requester_type', ['user', 'customer'])->default('user');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');

                $table->string('nama_lengkap');
                $table->string('jabatan')->nullable();
                $table->string('departemen')->nullable();
                $table->string('entitas')->nullable();
                $table->string('email');
                $table->string('no_hp')->nullable();

                $table->unsignedBigInteger('atasan_user_id')->nullable();
                $table->foreign('atasan_user_id')->references('id')->on('users')->onDelete('set null');
                $table->string('atasan_nama')->nullable();
                $table->string('atasan_email')->nullable();

                $table->unsignedBigInteger('l1_approver_user_id')->nullable();
                $table->string('l1_approver_name')->nullable();
                $table->string('l1_approver_email')->nullable();

                $table->unsignedBigInteger('l2_approver_user_id')->nullable();
                $table->string('l2_approver_name')->nullable();
                $table->string('l2_approver_email')->nullable();

                $table->enum('status', ['pending_l1', 'pending_l2', 'approved', 'rejected', 'cancelled', 'expired'])->default('pending_l1');
                $table->boolean('needs_layer2')->default(false);
                $table->decimal('max_goods_price', 15, 2)->nullable();
                $table->decimal('goods_total', 15, 2)->nullable();
                $table->decimal('services_total', 15, 2)->nullable();
                $table->decimal('total_amount', 15, 2)->default(0);

                $table->uuid('l1_token')->unique()->nullable();
                $table->uuid('l2_token')->unique()->nullable();
                $table->timestamp('l1_approved_at')->nullable();
                $table->timestamp('l2_approved_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();
                $table->timestamp('expired_at')->nullable();
                $table->timestamp('cancelled_at')->nullable();
                $table->unsignedBigInteger('rejected_by_user_id')->nullable();
                $table->text('reject_reason')->nullable();
                $table->text('notes')->nullable();

                $table->timestamps();

                $table->index('status');
                $table->index('request_no');
                $table->index('user_id');
                $table->index('customer_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ga_requests');
    }
}