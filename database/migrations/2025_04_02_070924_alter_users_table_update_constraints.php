<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTableUpdateConstraints extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // user_id を NOT NULL & UNIQUE に
            $table->string('user_id')->nullable(false)->change();

            // name, role_type, employee_type を NOT NULL に
            $table->string('name')->nullable(false)->change();
            $table->tinyInteger('role_type')->nullable(false)->change();
            $table->tinyInteger('employee_type')->nullable(false)->change();

            // email を NULLABLE に変更（元が NOT NULL の場合）
            $table->string('email')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // 元に戻す処理（必要に応じて）
            $table->dropUnique(['user_id']);
            $table->string('user_id')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->tinyInteger('role_type')->nullable()->change();
            $table->tinyInteger('employee_type')->nullable()->change();
            $table->string('email')->nullable(false)->change();
        });
    }
}
