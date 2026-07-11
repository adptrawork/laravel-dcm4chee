<?php

use App\Models\User;
use App\Models\WorklistItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(WorklistItem::class)->nullable()->constrained()->nullOnDelete();
            $table->string('study_instance_uid')->nullable();
            $table->string('accession_number')->nullable();
            $table->foreignIdFor(User::class, 'radiologist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('clinical_history')->nullable();
            $table->text('findings')->nullable();
            $table->text('impression')->nullable();
            $table->text('conclusion')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
