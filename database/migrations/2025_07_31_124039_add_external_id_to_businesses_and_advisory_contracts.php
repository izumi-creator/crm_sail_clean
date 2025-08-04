<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('external_id')->nullable()->unique()->after('id');
        });

        Schema::table('advisory_contracts', function (Blueprint $table) {
            $table->string('external_id')->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });

        Schema::table('advisory_contracts', function (Blueprint $table) {
            $table->dropColumn('external_id');
        });
    }
};
