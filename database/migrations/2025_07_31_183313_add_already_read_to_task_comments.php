<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('task_comments', function (Blueprint $table) {
            $table->boolean('already_read')->default(false)->after('to3_id');
            $table->boolean('already_read2')->default(false)->after('already_read');
            $table->boolean('already_read3')->default(false)->after('already_read2');
        });
    }
    
    public function down(): void
    {
        Schema::table('task_comments', function (Blueprint $table) {
            $table->dropColumn(['already_read', 'already_read2', 'already_read3']);
        });
    }
};
