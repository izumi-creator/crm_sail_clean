<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->bigIncrements('id'); // 相談ID

            // 先に存在している clients テーブルへの外部キー
            $table->foreignId('client_id')->constrained();

            // 必須項目であるが、トラブル時を想定してnullableとする
            $table->foreignId('consultation_id')->nullable()->constrained();

            // 未作成のテーブルへの外部キーは今は定義しない
            $table->unsignedBigInteger('advisory_id')->nullable();

            $table->tinyInteger('consultation_party'); // 区分
            $table->string('title', 255); // 件名
            $table->tinyInteger('status'); // ステータス
            $table->string('status_detail', 255)->nullable(); // ステータス詳細
            $table->string('case_summary', 1000)->nullable(); // 事件概要
            $table->string('special_notes', 1000)->nullable(); // 特記事項
            $table->tinyInteger('inquirytype')->nullable(); // 問い合せ形態
            $table->tinyInteger('consultationtype')->nullable(); // 相談形態
            $table->tinyInteger('case_category')->nullable(); // 事件分野
            $table->tinyInteger('case_subcategory')->nullable(); // 事件分野（詳細）
            $table->date('appointment_date')->nullable(); // 受任日
            $table->date('close_date')->nullable(); // 終結日
            $table->tinyInteger('close_notreason')->nullable(); // クローズ理由
            $table->date('status_limitday')->nullable(); // 時効完成日
            $table->tinyInteger('office_id')->nullable(); // 取扱事務所
            $table->foreignId('lawyer_id')->nullable()->constrained('users'); // 担当弁護士
            $table->foreignId('lawyer2_id')->nullable()->constrained('users'); // 担当弁護士2
            $table->foreignId('lawyer3_id')->nullable()->constrained('users'); // 担当弁護士3
            $table->foreignId('paralegal_id')->nullable()->constrained('users'); // 担当パラリーガル
            $table->foreignId('paralegal2_id')->nullable()->constrained('users'); // 担当パラリーガル2
            $table->foreignId('paralegal3_id')->nullable()->constrained('users'); // 担当パラリーガル3
            $table->longText('duedate_memo')->nullable(); // 期日メモ：Salesforceに合わせて最大約5万文字
            $table->string('feefinish_prospect', 255)->nullable(); // 見込理由
            $table->string('feesystem', 255)->nullable(); // 報酬体系
            $table->integer('sales_prospect')->nullable(); // 売上見込
            $table->integer('feesystem_initialvalue')->nullable(); // 売上見込（初期値）
            $table->date('sales_reason_updated')->nullable(); // 売上見込更新日
            $table->date('enddate_prospect')->nullable(); // 終了時期見込
            $table->date('enddate_prospect_initialvalue')->nullable(); // 終了時期見込（初期値）
            $table->tinyInteger('delay_check')->nullable(); // ディレイチェック
            $table->integer('deposit')->nullable(); // 着手金
            $table->integer('performance_reward')->nullable(); // 成果報酬
            $table->integer('difference')->nullable(); // 差額
            $table->integer('requestfee_initialvalue')->nullable(); // 預り依頼金（予定）
            $table->integer('requestfee')->nullable(); // 預り依頼金
            $table->integer('requestfee_balance')->nullable(); // 預り金残

            // 養育費関連
            $table->tinyInteger('childsupport_collect')->nullable(); // 養育費回収フラグ
            $table->tinyInteger('childsupport_phase')->nullable(); // フェーズ
            $table->integer('childsupport_monthly_fee')->nullable(); // 養育費月額
            $table->integer('childsupport_monthly_remuneration')->nullable(); // 養育費月額報酬
            $table->integer('childsupport_notcollected_amount')->nullable(); // 未回収金額
            $table->integer('childsupport_remittance_amount')->nullable(); // 依頼者送金額
            $table->date('childsupport_payment_date')->nullable(); // 支払日
            $table->date('childsupport_start_payment')->nullable(); // 支払期間（開始）
            $table->date('childsupport_end_payment')->nullable(); // 支払期間（終了）
            $table->string('childsupport_deposit_account', 255)->nullable(); // 入金先口座
            $table->date('childsupport_deposit_date')->nullable(); // 入金日
            $table->string('childsupport_transfersource_name', 255)->nullable(); // 振込元口座名義
            $table->date('childsupport_repayment_date')->nullable(); // 返金日
            $table->string('childsupport_financialinstitution_name', 255)->nullable(); // 返金先口座の金融機関名
            $table->string('childsupport_refundaccount_name', 255)->nullable(); // 返金先口座名義
            $table->tinyInteger('childsupport_temporary_payment')->nullable(); // 臨時払いの有無
            $table->string('childsupport_memo', 1000)->nullable(); // 備考

            $table->tinyInteger('route')->nullable(); // 流入経路
            $table->tinyInteger('routedetail')->nullable(); // 流入経路（詳細）
            $table->string('introducer', 255)->nullable(); // 紹介者
            $table->string('introducer_others', 255)->nullable(); // 紹介者その他
            $table->string('comment', 255)->nullable(); // コメント
            $table->string('progress_comment', 255)->nullable(); // 進捗コメント
            $table->timestamps();

            // インデックス
            $table->index('client_id');
            $table->index('consultation_id');
            // 未作成テーブル
            //$table->index('advisory_id');
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
        Schema::dropIfExists('business');
    }
};
