<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('related_parties', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->after('manager_department');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
        });
    }
    
    public function down(): void
    {
        Schema::table('related_parties', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
