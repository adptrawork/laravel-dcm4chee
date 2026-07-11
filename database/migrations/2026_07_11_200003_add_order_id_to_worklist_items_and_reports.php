<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worklist_items', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('server_id')->constrained()->nullOnDelete();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreignId('order_id')->nullable()->after('worklist_item_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });

        Schema::table('worklist_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
            $table->dropColumn('order_id');
        });
    }
};
