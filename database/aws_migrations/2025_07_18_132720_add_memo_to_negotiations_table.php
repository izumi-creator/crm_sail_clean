<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negotiations', function (Blueprint $table) {
            $table->longText('memo')->nullable()->after('notify_person_in');
        });
    }
    
    public function down(): void
    {
        Schema::table('negotiations', function (Blueprint $table) {
            $table->dropColumn(['memo']);
        });
    }
};
