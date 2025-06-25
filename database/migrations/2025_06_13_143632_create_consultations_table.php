<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->bigIncrements('id'); // 相談ID

            // 外部キー20250625見直し
            $table->foreignId('client_id')->constrained(); // クライアントID（外部キー）
            $table->foreignId('business_id')->nullable()->constrained();
            $table->foreignId('advisory_consultation_id')->nullable()->constrained();

            $table->tinyInteger('consultation_party'); // 区分
            $table->string('title', 255); // 件名
            $table->tinyInteger('status'); // ステータス
            $table->string('status_detail', 255)->nullable(); // ステータス詳細
            $table->string('case_summary', 1000)->nullable(); // 事件概要
            $table->string('special_notes', 1000)->nullable(); // 特記事項
            $table->string('inquirycontent', 1000)->nullable(); // 問合せ内容
            $table->dateTime('firstchoice_datetime')->nullable(); // 第一希望日
            $table->dateTime('secondchoice_datetime')->nullable(); // 第二希望日
            $table->tinyInteger('inquirytype')->nullable(); // 問い合せ形態
            $table->tinyInteger('consultationtype')->nullable(); // 相談形態
            $table->tinyInteger('case_category')->nullable(); // 事件分野
            $table->tinyInteger('case_subcategory')->nullable(); // 事件分野（詳細）
            $table->tinyInteger('opponent_confliction')->nullable(); // 利益相反確認
            $table->date('consultation_receptiondate')->nullable(); // 相談受付日
            $table->date('consultation_firstdate')->nullable(); // 初回相談日
            $table->date('enddate')->nullable(); // 終了日
            $table->tinyInteger('consultation_notreason')->nullable(); // 相談に至らなかった理由
            $table->tinyInteger('consultation_feedback')->nullable(); // 相談後のフィードバック
            $table->string('reason_termination', 255)->nullable(); // 相談終了理由
            $table->string('reason_termination_detail', 255)->nullable(); // 相談終了理由（詳細）
            $table->tinyInteger('office_id')->nullable(); // 取扱事務所
            $table->foreignId('lawyer_id')->nullable()->constrained('users'); // 担当弁護士
            $table->foreignId('lawyer2_id')->nullable()->constrained('users'); // 担当弁護士2
            $table->foreignId('lawyer3_id')->nullable()->constrained('users'); // 担当弁護士3
            $table->foreignId('paralegal_id')->nullable()->constrained('users'); // 担当パラリーガル
            $table->foreignId('paralegal2_id')->nullable()->constrained('users'); // 担当パラリーガル2
            $table->foreignId('paralegal3_id')->nullable()->constrained('users'); // 担当パラリーガル3
            $table->string('feefinish_prospect', 255)->nullable(); // 見込理由
            $table->string('feesystem', 255)->nullable(); // 報酬体系
            $table->integer('sales_prospect')->nullable(); // 売上見込
            $table->integer('feesystem_initialvalue')->nullable(); // 売上見込（初期値）
            $table->date('sales_reason_updated')->nullable(); // 売上見込更新日
            $table->date('enddate_prospect')->nullable(); // 終了時期見込
            $table->date('enddate_prospect_initialvalue')->nullable(); // 終了時期見込（初期値）
            $table->tinyInteger('route')->nullable(); // 流入経路
            $table->tinyInteger('routedetail')->nullable(); // 流入経路（詳細）
            $table->string('introducer', 255)->nullable(); // 紹介者
            $table->string('introducer_others', 255)->nullable(); // 紹介者その他
            $table->timestamps();

            // インデックス
            $table->index('client_id');
            $table->index('business_id');
            $table->index('advisory_consultation_id');
            $table->index('consultation_party');
            $table->index('inquirytype');
            $table->index('title');
            $table->index('status');
            $table->index('lawyer_id');
            $table->index('lawyer2_id');
            $table->index('lawyer3_id');
            $table->index('paralegal_id');
            $table->index('paralegal2_id');
            $table->index('paralegal3_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
