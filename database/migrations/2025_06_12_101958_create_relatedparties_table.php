<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('related_parties', function (Blueprint $table) {
            $table->id(); // 関係者ID

            // 外部キー
            // 20250630見直し、client_idは削除
            $table->foreignId('consultation_id')->nullable()->constrained();
            $table->foreignId('business_id')->nullable()->constrained();
            $table->foreignId('advisory_consultation_id')->nullable()->constrained();

            // 区分・分類・種別・立場
            $table->tinyInteger('relatedparties_party');
            $table->tinyInteger('relatedparties_class');
            $table->tinyInteger('relatedparties_type');
            $table->tinyInteger('relatedparties_position')->nullable();
            $table->string('relatedparties_position_details', 255)->nullable();
            $table->string('relatedparties_explanation', 1000)->nullable();

            // 詳細情報
            $table->string('relatedparties_name_kanji', 255);
            $table->string('relatedparties_name_kana', 255)->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('phone_number', 15)->nullable();
            $table->string('phone_number2', 15)->nullable();
            $table->string('fax', 15)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('email2', 100)->nullable();
            $table->string('relatedparties_postcode', 10)->nullable();
            $table->string('relatedparties_address', 255)->nullable();
            $table->string('relatedparties_address2', 255)->nullable();
            $table->string('placeofwork', 255)->nullable();
            $table->string('manager_name_kanji', 255)->nullable();
            $table->string('manager_name_kana', 255)->nullable();
            $table->string('manager_post', 100)->nullable();
            $table->string('manager_department', 100)->nullable();

            $table->timestamps();

            // インデックス
            $table->index('consultation_id');
            $table->index('business_id');
            $table->index('advisory_consultation_id');
            $table->index('relatedparties_class');
            $table->index('relatedparties_type');
            $table->index('relatedparties_position');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('related_parties');
    }
};