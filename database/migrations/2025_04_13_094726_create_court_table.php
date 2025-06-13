<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();
            $table->string('court_name')->unique(); // 名前にユニーク制約
            $table->string('court_type')->comment('システム権限（1:最高裁判所, 2:高等裁判所, 3:地方裁判所, 4:家庭裁判所, 5:簡易裁判所）');
            $table->string('postal_code')->nullable();
            $table->string('location')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('importantnotes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};
