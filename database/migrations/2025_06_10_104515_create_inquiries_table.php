<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->bigIncrements('id'); // 問合せID

            $table->tinyInteger('status')->default(1);
            $table->dateTime('receptiondate');

            $table->string('inquiries_name_kanji', 255);
            $table->string('inquiries_name_kana', 255);

            $table->string('last_name_kanji', 100);
            $table->string('first_name_kanji', 155);
            $table->string('last_name_kana', 100);
            $table->string('first_name_kana', 155);

            $table->string('corporate_name', 255)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('state', 10)->nullable();

            $table->dateTime('firstchoice_datetime')->nullable();
            $table->dateTime('secondchoice_datetime')->nullable();

            $table->string('inquirycontent', 1000)->nullable();

            $table->tinyInteger('route')->nullable();
            $table->tinyInteger('routedetail')->nullable();

            $table->string('averageovertimehoursperweek', 10)->nullable();
            $table->string('monthlyincome', 10)->nullable();
            $table->string('lengthofservice', 10)->nullable();

            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->string('explanation', 1000)->nullable();

            $table->unsignedBigInteger('consultation_id')->nullable();

            $table->timestamps();

            // インデックス
            $table->index('status');
            $table->index('receptiondate');
            $table->index('routedetail');
            $table->index('manager_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};