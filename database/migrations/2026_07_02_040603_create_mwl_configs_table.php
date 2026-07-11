<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mwl_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained()->cascadeOnDelete();
            $table->string('default_aet')->default('PZDR');
            $table->string('default_modality', 16)->nullable();
            $table->string('default_physician')->nullable();
            $table->string('default_room')->nullable();
            $table->string('default_institution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mwl_configs');
    }
};
