<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mariadb' || $driver === 'mysql') {
            DB::statement("ALTER TABLE worklist_items ALTER status SET DEFAULT 'registered'");
        }

        DB::table('worklist_items')->where('status', 'waiting')->update(['status' => 'registered']);
    }

    public function down(): void
    {
        DB::table('worklist_items')->where('status', 'registered')->update(['status' => 'waiting']);

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mariadb' || $driver === 'mysql') {
            DB::statement("ALTER TABLE worklist_items ALTER status SET DEFAULT 'waiting'");
        }
    }
};
