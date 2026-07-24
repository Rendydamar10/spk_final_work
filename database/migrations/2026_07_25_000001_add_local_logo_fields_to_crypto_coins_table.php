<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crypto_coins', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('image');
            $table->text('logo_source_url')->nullable()->after('logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('crypto_coins', function (Blueprint $table) {
            $table->dropColumn(['logo_path', 'logo_source_url']);
        });
    }
};
