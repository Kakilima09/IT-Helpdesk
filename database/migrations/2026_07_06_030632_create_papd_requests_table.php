<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePapdRequestsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('papd_requests')) {
            Schema::create('papd_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id'); // pemohon
                $table->foreign('user_id')->references('id')->on('users');

                // Bagian A - Informasi Pemohon
                $table->string('nama_lengkap');
                $table->string('id_karyawan');
                $table->string('nik_ktp')->nullable();
                $table->string('jabatan');
                $table->string('departemen');
                $table->string('entitas')->nullable();
                $table->string('atasan_nama');
                $table->string('atasan_email');
                $table->string('no_hp');
                $table->string('email');
                $table->date('ttl');
                $table->string('no_paspor')->nullable();
                $table->date('exp_date_paspor')->nullable();

                // Bagian B - Perjalanan Dinas
                $table->enum('jenis_perjalanan', ['domestik', 'internasional']);
                $table->string('nama_paspor', 255)->nullable();
                $table->string('kota_tujuan');
                $table->text('agenda');
                $table->string('no_sppd')->nullable();
                $table->date('tanggal_keberangkatan');
                $table->time('jam_keberangkatan');
                $table->date('tanggal_kepulangan')->nullable();
                $table->time('jam_kepulangan')->nullable();
                $table->integer('durasi_hari'); // atau decimal
                $table->string('pembebanan_biaya')->nullable();

                // Bagian C1 - Transportasi
                $table->enum('moda_transportasi', ['pesawat', 'kereta', 'bus', 'kapal', 'whoosh', 'lainnya']);
                $table->string('kelas')->nullable(); // Ekonomi/Bisnis/Eksekutif/VIP
                $table->string('rute')->nullable();
                $table->string('detail_maskapai')->nullable();
                $table->string('no_penerbangan')->nullable();
                $table->boolean('bagasi_tambahan')->default(false);
                $table->boolean('transportasi_lokal')->default(false);
                $table->json('opsi_transportasi')->nullable(); // ["insurance", "refundable", "lainnya"]

                // Bagian C2 - Akomodasi Hotel
                $table->boolean('hotel_reservasi')->default(false);
                $table->string('nama_hotel')->nullable();
                $table->string('lokasi_hotel')->nullable();
                $table->text('alamat_hotel')->nullable();
                $table->date('check_in')->nullable();
                $table->date('check_out')->nullable();
                $table->integer('jumlah_kamar')->nullable();
                $table->text('permintaan_khusus')->nullable();

                // Catatan
                $table->text('notes')->nullable();

                // Workflow & Status
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->uuid('approval_token')->unique()->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamp('rejected_at')->nullable();

                DB::statement("ALTER TABLE papd_requests MODIFY status ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending'");


                $table->timestamps();

                // Indeks
                $table->index('status');
                $table->index('user_id');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('papd_requests');
        DB::statement("ALTER TABLE papd_requests MODIFY status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
}