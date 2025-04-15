<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends \Illuminate\Database\Migrations\Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 🔽 email のユニークキーを削除
            $table->dropUnique('users_email_unique');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 🔁 元に戻す（必要なら）
            $table->unique('email');
        });
    }
};
