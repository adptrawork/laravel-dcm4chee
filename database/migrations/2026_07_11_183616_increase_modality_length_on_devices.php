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
        Schema::table('devices', function (Blueprint $table) {
            $table->string('modality', 32)->nullable()->change();
            $table->string('name', 255)->change();
            $table->string('ae_title', 255)->change();
            $table->string('hostname', 255)->change();
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->string('modality', 16)->nullable()->change();
            $table->string('name', 100)->change();
            $table->string('ae_title', 100)->change();
            $table->string('hostname', 100)->change();
        });
    }
};
