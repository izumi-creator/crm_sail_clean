<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->longText('case_summary')->nullable()->change();
            $table->longText('special_notes')->nullable()->change();
            $table->longText('inquirycontent')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->string('case_summary', 1000)->nullable()->change();
            $table->string('special_notes', 1000)->nullable()->change();
            $table->string('inquirycontent', 1000)->nullable()->change();
        });
    }
};
