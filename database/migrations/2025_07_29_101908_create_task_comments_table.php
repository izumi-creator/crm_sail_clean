<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('task_comments', function (Blueprint $table) {
            $table->bigIncrements('id'); 
            // 紐づくタスク
            $table->foreignId('task_id')->constrained();
            $table->string('comment', 255);

            // 投稿者
            $table->foreignId('from_id')->constrained('users');

            // 宛先1〜3（nullable）
            $table->foreignId('to_id')->nullable()->constrained('users');
            $table->foreignId('to2_id')->nullable()->constrained('users');
            $table->foreignId('to3_id')->nullable()->constrained('users');

            // 作成者・更新者
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_comments');
    }
};