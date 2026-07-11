<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('device_id')->nullable()->after('procedure_id')
                ->constrained()->nullOnDelete();
            $table->dropColumn('station_ae_title');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('station_ae_title')->nullable()->after('scheduled_date');
            $table->dropForeign(['device_id']);
            $table->dropColumn('device_id');
        });
    }
};
