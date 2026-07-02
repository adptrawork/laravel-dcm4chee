<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('worklist_items', function (Blueprint $table) {
            $table->renameColumn('study_uid', 'study_instance_uid');

            $table->string('procedure_code')->nullable()->after('modality');
            $table->string('requested_procedure_id')->nullable()->after('procedure_code');
            $table->string('sps_id')->nullable()->after('requested_procedure_id');

            $table->timestamp('taken_at')->nullable()->after('sent_at');
            $table->timestamp('acquired_at')->nullable()->after('taken_at');
            $table->timestamp('archived_at')->nullable()->after('acquired_at');
            $table->timestamp('reported_at')->nullable()->after('archived_at');
            $table->timestamp('verified_at')->nullable()->after('reported_at');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('verified_at');

            $table->index('procedure_code');
            $table->index('study_instance_uid');
        });
    }

    public function down(): void
    {
        Schema::table('worklist_items', function (Blueprint $table) {
            $table->renameColumn('study_instance_uid', 'study_uid');

            $table->dropColumn([
                'procedure_code', 'requested_procedure_id', 'sps_id',
                'taken_at', 'acquired_at', 'archived_at',
                'reported_at', 'verified_at', 'verified_by',
            ]);
        });
    }
};
