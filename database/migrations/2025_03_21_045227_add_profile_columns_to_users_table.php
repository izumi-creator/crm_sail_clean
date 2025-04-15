<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('employee_type')->nullable()->comment('従業員区分（1:弁護士, 2:パラリーガル, 3:スタッフ,4:その他）');
            $table->tinyInteger('role_type')->nullable()->comment('システム権限（1:管理者, 2:一般）');
            $table->tinyInteger('office_id')->nullable()->comment('所属事務所（1:銀座事務所, 2:横浜事務所, 3:鎌倉事務所, 4:津田沼事務所, 5:浜松事務所, 6:その他））');
            $table->string('phone_number')->nullable();
            $table->string('phone_number2')->nullable();
            $table->string('email2')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'employee_type',
                'role_type',
                'office_id',
                'phone_number',
                'phone_number2',
                'email2',
            ]);
        });
    }
};
