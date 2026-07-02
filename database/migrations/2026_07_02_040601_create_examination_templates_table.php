<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examination_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('modality', 16);
            $table->text('description');
            $table->string('room')->nullable();
            $table->string('priority')->default('normal');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examination_templates');
    }
};
