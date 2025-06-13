<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('insurance_companies', function (Blueprint $table) {
            $table->renameColumn('type', 'insurance_type');
        });
    }

    public function down(): void
    {
        Schema::table('insurance_companies', function (Blueprint $table) {
            $table->renameColumn('insurance_type', 'type');
        });
    }
};

