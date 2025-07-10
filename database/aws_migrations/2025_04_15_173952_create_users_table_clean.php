<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
    
            // ログイン用ID（NOT NULL & UNIQUE）
            $table->string('user_id')->unique();
    
            // 氏名（NOT NULL）
            $table->string('name');
    
            // パスワード
            $table->string('password');
            
            // 区分情報（NOT NULL）
            $table->tinyInteger('employee_type');
            $table->tinyInteger('role_type');
    
            // 所属事務所（nullable可）
            $table->tinyInteger('office_id')->nullable();
    
            // メールアドレス（任意、ユニーク制約なし）
            $table->string('email')->nullable();
    
            // 補助情報
            $table->string('email2')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('phone_number2')->nullable();
    
            // セッション・トークン系
            $table->rememberToken();
            $table->timestamps();
        });
    
        // パスワードリセットトークン
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    
        // セッションテーブル（SESSION_DRIVER=database のため）
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
