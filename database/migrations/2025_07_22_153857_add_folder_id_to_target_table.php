<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->string('folder_id')->nullable()->after('introducer_others');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->string('folder_id')->nullable()->after('progress_comment');
        });

        Schema::table('advisory_contracts', function (Blueprint $table) {
            $table->string('folder_id')->nullable()->after('newyearscard');
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropColumn(['folder_id']);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn(['folder_id']);
        });

        Schema::table('advisory_contracts', function (Blueprint $table) {
            $table->dropColumn(['folder_id']);
        });
    }
};
