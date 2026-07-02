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
            $table->timestamp('last_echo_at')->nullable()->after('status');
            $table->timestamp('last_mwl_query_at')->nullable()->after('last_echo_at');
            $table->timestamp('last_store_at')->nullable()->after('last_mwl_query_at');
            $table->integer('queue_count')->default(0)->after('last_store_at');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['last_echo_at', 'last_mwl_query_at', 'last_store_at', 'queue_count']);
        });
    }
};
