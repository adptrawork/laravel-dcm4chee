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
        Schema::create('procedures', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('modality', 16);
            $table->string('body_part', 64)->nullable();
            $table->integer('estimated_duration')->nullable()->comment('minutes');
            $table->string('default_physician')->nullable();
            $table->string('default_room')->nullable();
            $table->string('default_exposure')->nullable();
            $table->boolean('requires_contrast')->default(false);
            $table->text('contrast_detail')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedures');
    }
};
