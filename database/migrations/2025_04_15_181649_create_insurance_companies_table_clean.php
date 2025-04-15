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
        Schema::create('insurance_companies', function (Blueprint $table) {
            $table->id();
    
            // 保険会社名・種別（マスタで管理）
            $table->string('insurance_name')->unique();
            $table->string('insurance_type');
    
            // 窓口1
            $table->string('contactname')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
    
            // 窓口2
            $table->string('contactname2')->nullable();
            $table->string('phone_number2')->nullable();
            $table->string('email2')->nullable();
    
            // 備考（現時点ではvarcharでOK）
            $table->string('importantnotes')->nullable();
    
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('insurance_companies');
    }
};
