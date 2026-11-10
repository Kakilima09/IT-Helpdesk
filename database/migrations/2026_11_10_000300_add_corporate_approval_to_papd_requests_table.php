<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCorporateApprovalToPapdRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->enum('corporate_status', ['pending', 'approved', 'rejected'])
                ->nullable()
                ->after('closing_status');
            $table->timestamp('corporate_approved_at')->nullable()->after('corporate_status');
            $table->unsignedBigInteger('corporate_approved_by')->nullable()->after('corporate_approved_at');
            $table->text('corporate_note')->nullable()->after('corporate_approved_by');

            $table->index('corporate_status');
        });

        // Data lama yang sudah approved dianggap sudah disetujui corporate
        DB::table('papd_requests')
            ->where('status', 'approved')
            ->update(['corporate_status' => 'approved']);
    }

    public function down()
    {
        Schema::table('papd_requests', function (Blueprint $table) {
            $table->dropIndex(['corporate_status']);
            $table->dropColumn([
                'corporate_status',
                'corporate_approved_at',
                'corporate_approved_by',
                'corporate_note',
            ]);
        });
    }
}
