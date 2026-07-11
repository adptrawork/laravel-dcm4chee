<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('worklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->nullable()->constrained()->nullOnDelete();
            $table->string('accession_number')->nullable()->index();
            $table->string('patient_name');
            $table->string('patient_id');
            $table->string('modality', 16);
            $table->text('procedure_description');
            $table->string('requesting_physician')->nullable();
            $table->string('scheduled_date')->nullable();
            $table->string('scheduled_time')->nullable();
            $table->string('status')->default('waiting');
            $table->string('study_uid')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('worklist_items');
    }
};
