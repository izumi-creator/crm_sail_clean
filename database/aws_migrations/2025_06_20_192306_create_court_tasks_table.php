<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    public function up(): void
    {
        Schema::create('court_tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            //外部キー
            $table->foreignId('court_id')->constrained();
            $table->foreignId('business_id')->constrained();

            $table->tinyInteger('status');
            $table->string('status_detail', 255)->nullable();
            $table->string('department', 255)->nullable();
            $table->string('judge', 255)->nullable();
            $table->string('clerk', 255)->nullable();
            $table->string('tel_direct', 15)->nullable();
            $table->string('fax_direct', 15)->nullable();
            $table->string('email_direct', 100)->nullable();
            $table->tinyInteger('task_category')->nullable();
            $table->string('task_title', 255)->nullable();
            $table->longText('task_content')->nullable(); 
            $table->foreignId('lawyer_id')->nullable()->constrained('users');
            $table->foreignId('paralegal_id')->nullable()->constrained('users');
            $table->datetime('deadline')->nullable();
            $table->time('move_time')->nullable();
            $table->longText('memo')->nullable(); 
            $table->timestamps();

            // インデックス
            $table->index('court_id');
            $table->index('business_id');
            $table->index('status');
            $table->index('task_category');
            $table->index('task_title');
            $table->index('lawyer_id');
            $table->index('paralegal_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('court_tasks');
    }
};
