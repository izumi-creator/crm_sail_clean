<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('court_tasks', function (Blueprint $table) {
            $table->string('case_number')->nullable()->after('status_detail');
        });
    }

    public function down(): void
    {
        Schema::table('court_tasks', function (Blueprint $table) {
            $table->dropColumn('case_number');
        });
    }
};
