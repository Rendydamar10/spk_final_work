<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('criteria') || ! Schema::hasColumn('criteria', 'type')) {
            return;
        }

        // MySQL ENUM rejects invalid values before SawService can perform
        // domain validation. A short VARCHAR keeps database storage simple
        // while the service remains the single source of validation.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE criteria MODIFY type VARCHAR(20) NOT NULL");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('criteria') || ! Schema::hasColumn('criteria', 'type')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::table('criteria')
                ->whereNotIn('type', ['benefit', 'cost'])
                ->update(['type' => 'benefit']);

            DB::statement("ALTER TABLE criteria MODIFY type ENUM('benefit','cost') NOT NULL");
        }
    }
};
