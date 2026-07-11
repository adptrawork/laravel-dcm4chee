<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->string('accession_number')->unique();
            $table->foreignId('procedure_id')->nullable()->constrained()->nullOnDelete();
            $table->string('modality')->nullable();
            $table->string('requesting_physician')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->string('status')->default('pending');
            $table->string('priority')->default('routine');
            $table->date('scheduled_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
