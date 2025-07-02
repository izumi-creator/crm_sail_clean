<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use App\Models\Inquiry;
use App\Models\RelatedParty;
use App\Models\Consultation;
use App\Models\Business;
use App\Models\AdvisoryContract;
use App\Models\AdvisoryConsultation;
use App\Models\Task;
use App\Models\Negotiation;
use App\Models\CourtTask;
use App\Models\InsuranceCompany;
use App\Models\Court;
use App\Models\Room;


use Carbon\Carbon;

class ExportController extends Controller
{
    public function download(Request $request)
    {
        $type = $request->input('type');
        $timestamp = Carbon::now()->setTimezone('Asia/Tokyo')->format('YmdHi');

        // ✅ 共通format_rowヘルパー関数（コントローラー内で定義）
        $formatRowHelper = function ($columns, $masterMaps) {
            return function ($row) use ($columns, $masterMaps) {
                return collect($columns)->map(function ($col) use ($row, $masterMaps) {
                    if (isset($masterMaps[$col])) {
                        $masterKey = $masterMaps[$col];
                        return config("master.$masterKey")[$row->$col] ?? '';
                    }
                    return $row->$col;
                })->all();
            };
        };

        // ✅ export対象定義マップ（共通化）
        $exportables = [
            'clients' => [
                'model' => Client::class,
                'table' => 'clients',
                'filename_prefix' => 'clients',
                'excluded' => [],
                'headers' => [
                    'ID', '個人法人区分', 'クライアント名（漢字）', 'クライアント名（ふりがな）',
                    '取引先名略称', '電話番号', '電話番号2', '携帯電話', '自宅電話番号', '自宅連絡不可区分',
                    '電話番号（第一連絡先）', '電話番号（第二連絡先）', 'FAX', 'メールアドレス1', 'メールアドレス2',
                    '住所_郵便番号', '住所_都道府県', '住所_市区郡', '住所_町名・番地', 
                    '住所_名（漢字）', '住所_名（ふりがな）', '住所（郵送先）_郵便番号', '住所（郵送先）_都道府県', 
                    '住所（郵送先）_市区郡', '住所（郵送先）_町名・番地', '住所（郵送先）_請求先名（漢字）', '住所（郵送先）_請求先名（ふりがな）', '連絡先特記事項', 
                    '姓（漢字）', '名（漢字）', '姓（ふりがな）', '名（ふりがな）', '生年月日', '本人確認書1', '本人確認書2', '本人確認書3', 
                    '年賀状を送る', '暑中見舞いを送る', '事務所報を送る', '交際情報履歴を自動作成', 
                    '取引先責任者_姓（漢字）', '取引先責任者_名（漢字）', '取引先責任者_姓（ふりがな）', '取引先責任者_名（ふりがな）', 
                    '取引先責任者_電話番号', '取引先責任者_携帯電話', '取引先責任者_自宅電話番号', 
                    '取引先責任者_メールアドレス1', '取引先責任者_メールアドレス2', '取引先責任者_FAX', 
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'client_type' => 'client_types',
                    'not_home_contact' => 'not_home_contacts',
                    'identification_document1' => 'identification_documents',
                    'identification_document2' => 'identification_documents',
                    'identification_document3' => 'identification_documents',
                    'send_newyearscard' => 'send_types',
                    'send_summergreetingcard' => 'send_types',
                    'send_office_news' => 'send_types',
                    'send_autocreation' => 'send_autocreations',
                ],
            ],
            'users' => [
                'model' => User::class,
                'table' => 'users',
                'filename_prefix' => 'users',
                'excluded' => ['password', 'remember_token'],
                'headers' => [
                    'ID', 'ユーザID', '氏名', 
                    '従業員区分', 'システム権限', '所属事務所',
                    'メールアドレス1',  'メールアドレス2',
                    '電話番号1', '電話番号2',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'employee_type' => 'employee_types',
                    'role_type'     => 'role_types',
                    'office_id'     => 'offices',
                ],
            ],
            'inquiries' => [
                'model' => Inquiry::class,
                'table' => 'inquiries',
                'filename_prefix' => 'inquiries',
                'excluded' => [],
                'headers' => [
                    'ID', 'ステータス', '問合せ受付日',
                    'お名前（漢字）', 'お名前（ふりがな）', '姓（漢字）', '名（漢字）', '姓（ふりがな）', '名（ふりがな）',
                    '会社名', '電話番号', 'メールアドレス', '都道府県', '第一希望日', '第二希望日',
                    'お問合せ内容', '流入経路', '流入経路（詳細）',
                    '1週間当たりの平均残業時間', '月収', '勤続年数', '担当者ID', '説明', '相談ID',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'status'  => 'inquiry_status',
                    'route'   => 'routes',
                    'routedetail'   => 'routedetails',
                ],
            ],
            'related_parties' => [
                'model' => RelatedParty::class,
                'table' => 'related_parties',
                'filename_prefix' => 'related_parties',
                'excluded' => [],
                'headers' => [
                    'ID', '相談ID', '受任案件ID', '顧問相談ID',
                    '区分', '分類', '種別', '立場', '立場詳細', '説明',
                    '関係者名（漢字）', '関係者名（ふりがな）',
                    '携帯', '電話', '電話2', 'Fax', 'メール', 'メール2',
                    '郵便番号', '住所', '住所2', '勤務先',
                    '担当者名（漢字）', '担当者名（ふりがな）', '役職', '部署',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'relatedparties_party'    => 'relatedparties_parties',
                    'relatedparties_class'    => 'relatedparties_classes',
                    'relatedparties_type'     => 'relatedparties_types',
                    'relatedparties_position' => 'relatedparties_positions',
                ],
            ],
            'consultations' => [
                'model' => Consultation::class,
                'table' => 'consultations',
                'filename_prefix' => 'consultations',
                'excluded' => [],
                'headers' => [
                    'ID', 'クライアントID', '受任案件ID', '顧問相談ID',
                    '区分', '件名', 'ステータス', 'ステータス詳細',
                    '事件概要', '特記事項', '問合せ内容',
                    '第一希望日', '第二希望日',
                    '問い合せ形態', '相談形態', '事件分野', '事件分野（詳細）', '利益相反確認', '利益相反実施日',
                    '相談受付日', '初回相談日', '終了日',
                    '相談に至らなかった理由', '相談後のフィードバック', '相談終了理由', '相談終了理由（詳細）',
                    '取扱事務所','担当弁護士ID', '担当弁護士2ID', '担当弁護士3ID',
                    '担当パラリーガルID', '担当パラリーガル2ID', '担当パラリーガル3ID',
                    '見込理由', '報酬体系', '売上見込', '売上見込（初期値）', '売上見込更新日',
                    '終了時期見込', '終了時期見込（初期値）', '流入経路', '流入経路（詳細）', '紹介者', '紹介者その他',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'consultation_party'    => 'consultation_parties',
                    'status'                => 'consultation_statuses',
                    'inquirytype'           => 'inquirytypes',
                    'consultationtype'      => 'consultation_types',
                    'case_category'         => 'case_categories',
                    'case_subcategory'      => 'case_subcategories',
                    'opponent_confliction'  => 'opponent_conflictions',
                    'consultation_notreason'    => 'consultation_notreasons',
                    'consultation_feedback'     => 'consultation_feedbacks',
                    'office_id'      => 'offices',
                    'route'      => 'routes',
                    'routedetail'      => 'routedetails',
                ],
            ],
            'businesses' => [
                'model' => Business::class,
                'table' => 'businesses',
                'filename_prefix' => 'businesses',
                'excluded' => [],
                'headers' => [
                    'ID', 'クライアントID', '相談ID',
                    '区分', '件名', 'ステータス', 'ステータス詳細',
                    '事件概要', '特記事項', '問い合せ形態', '相談形態', '事件分野', '事件分野（詳細）',
                    '受任日', '終結日', 'クローズ理由', '時効完成日',
                    '取扱事務所', '担当弁護士ID', '担当弁護士2ID', '担当弁護士3ID',
                    '担当パラリーガルID', '担当パラリーガル2ID', '担当パラリーガル3ID', '期日メモ',
                    '見込理由', '報酬体系', '売上見込', '売上見込（初期値）', '売上見込更新日', '終了時期見込', '終了時期見込（初期値）', 'ディレイチェック',
                    '着手金', '成果報酬', '差額', '預り依頼金（予定）', '預り依頼金', '預り金残',
                    '養育費回収フラグ', 'フェーズ', '養育費月額', '養育費月額報酬', '未回収金額', '依頼者送金額',
                    '支払日', '支払期間（開始）', '支払期間（終了）', '入金先口座', '入金日', '振込元口座名義', '返金日',
                    '返金先口座の金融機関名', '返金先口座名義', '臨時払いの有無', '備考',
                    '流入経路', '流入経路（詳細）', '紹介者', '紹介者その他', 'コメント', '進捗コメント',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'consultation_party'    => 'consultation_parties',
                    'status'                => 'business_statuses',
                    'inquirytype'           => 'inquirytypes',
                    'consultationtype'      => 'consultation_types',
                    'case_category'         => 'case_categories',
                    'case_subcategory'      => 'case_subcategories',
                    'close_notreason'       => 'close_notreasons',
                    'office_id'             => 'offices',
                    'childsupport_phase'    => 'childsupport_phases',
                    'route'                 => 'routes',
                    'routedetail'           => 'routedetails',
                ],
            ],
            'advisory_contracts' => [
                'model' => AdvisoryContract::class,
                'table' => 'advisory_contracts',
                'filename_prefix' => 'advisory_contracts',
                'excluded' => [],
                'headers' => [
                    'ID', 'クライアントID',
                    '区分', '件名', 'ステータス', '利益相反確認', '利益相反実施日',
                    '説明', '特記事項', '契約開始日', '契約終了日',
                    '顧問料月額', '契約期間（月）', '初回相談日', '支払区分', '自動引落番号',
                    '支払方法', '引落依頼額', '引落内訳', '引落更新日',
                    '取扱事務所', '担当弁護士ID', '担当弁護士2ID', '担当弁護士3ID',
                    '担当パラリーガルID', '担当パラリーガル2ID', '担当パラリーガル3ID',
                    'ソース', 'ソース詳細', '紹介者その他',
                    'お中元お歳暮', '年賀状',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'advisory_party'        => 'advisory_parties',
                    'status'                => 'advisory_contracts_statuses',
                    'opponent_confliction'  => 'opponent_conflictions',
                    'payment_category'      => 'payment_categories',
                    'payment_method'        => 'payment_methods',
                    'office_id'             => 'offices',
                    'source'                => 'routes',
                    'source_detail'         => 'routedetails',
                    'gift'                  => 'gifts',
                    'newyearscard'          => 'newyearscards',
                ],
            ],
            'advisory_consultations' => [
                'model' => AdvisoryConsultation::class,
                'table' => 'advisory_consultations',
                'filename_prefix' => 'advisory_consultations',
                'excluded' => [],
                'headers' => [
                    'ID', 'クライアントID', '顧問契約ID', '相談ID',
                    '区分', '件名', 'ステータス', '利益相反チェック', '利益相反実施日',
                    '相談概要', '特記事項', '相談開始日', '相談終了日', '解決理由',
                    '取扱事務所', '担当弁護士ID', '担当弁護士2ID', '担当弁護士3ID',
                    '担当パラリーガルID', '担当パラリーガル2ID', '担当パラリーガル3ID',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'advisory_party'        => 'advisory_parties',
                    'status'                => 'advisory_consultations_statuses',
                    'opponent_confliction'  => 'opponent_conflictions',
                    'close_reason'          => 'close_reasons',
                    'office_id'             => 'offices',
                ],
            ],
            'tasks' => [
                'model' => Task::class,
                'table' => 'tasks',
                'filename_prefix' => 'tasks',
                'excluded' => [],
                'headers' => [
                    'ID', '関連先区分（登録時）', '相談ID', '受任案件ID', '顧問契約ID', '顧問相談ID',
                    '大区分', '小区分', '件名', 'ステータス', '既読チェック',
                    '登録日', '期限日', '期限時間',
                    'タスク内容', '依頼者ID', '対応者ID',
                    '添付名1', '添付名2', '添付名3', '添付リンク1', '添付リンク2', '添付リンク3',
                    '運送業者', '追跡番号', '電話通知チェック', '通知タイプ',
                    '宛先', '電話番号', '着信電話番号', '発信電話番号', '着信内線番号', '発信内線番号',
                    '担当通知', 'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'related_party' => 'related_parties',
                    'record1'       => 'records_1',
                    'record2'       => 'records_2',
                    'status'        => 'task_statuses',
                    'carrier'       => 'carriers',
                ],
            ],
            'negotiations' => [
                'model' => Negotiation::class,
                'table' => 'negotiations',
                'filename_prefix' => 'negotiations',
                'excluded' => [],
                'headers' => [
                    'ID', '関連先区分（登録時）', '相談ID', '受任案件ID', '顧問契約ID', '顧問相談ID',
                    '大区分', '小区分', '件名', 'ステータス', '既読チェック',
                    '登録日', 'タスク内容', '依頼者ID', '対応者ID',
                    '添付名1', '添付名2', '添付名3', '添付リンク1', '添付リンク2', '添付リンク3',
                    '電話通知チェック', '通知タイプ',
                    '宛先', '電話番号', '着信電話番号', '発信電話番号', '着信内線番号', '発信内線番号',
                    '担当通知', 'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'related_party' => 'related_parties',
                    'record1'       => 'records_1',
                    'record2'       => 'records_2',
                    'status'        => 'task_statuses',
                ],
            ],
            'court_tasks' => [
                'model' => CourtTask::class,
                'table' => 'court_tasks',
                'filename_prefix' => 'court_tasks',
                'excluded' => [],
                'headers' => [
                    'ID', '裁判所マスタID', '受任案件ID',
                    'ステータス', 'ステータス詳細',
                    '担当係', '担当裁判官', '担当書記官', '電話（直通）', 'FAX（直通）', 'メール（直通）',
                    'タスク分類', 'タスク名', 'タスク内容', '担当弁護士ID', '担当パラリーガルID',
                    '期限', '移動時間', 'メモ欄',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'status'        => 'court_tasks_statuses',
                    'task_category' => 'court_task_categories',
                ],
            ],
            'insurances' => [
                'model' => InsuranceCompany::class,
                'table' => 'insurance_companies',
                'filename_prefix' => 'insurances',
                'excluded' => [],
                'headers' => [
                    'ID', '会社名', '保険会社区分',
                    '問合せ窓口1', '電話番号1', 'メール1',
                    '問合せ窓口2', '電話番号2', 'メール2',
                    '備考', 'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'insurance_type' => 'insurance_types',
                ],
            ],
            'courts' => [
                'model' => Court::class,
                'table' => 'courts',
                'filename_prefix' => 'courts',
                'excluded' => [],
                'headers' => [
                    'ID', '裁判所名', '裁判所区分', '郵便番号',
                    '所在地', '電話番号', '備考', 'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'court_type' => 'court_types',
                ],
            ],
            'rooms' => [
                'model' => Room::class,
                'table' => 'rooms',
                'filename_prefix' => 'rooms',
                'excluded' => [],
                'headers' => [
                    'ID', '部屋名', 'GoogleカレンダーID',
                    '場所', '備考',
                    'レコード作成日', 'レコード更新日',
                ],
                'master_maps' => [
                    'office_id' => 'offices_id',
                ],
            ],
        ];

        // ✅ 対象が正しいかチェック
        if (!isset($exportables[$type])) {
            return back()->with('error', 'データ種別が不正です');
        }

        $config = $exportables[$type];
        $model = $config['model'];
        $table = $config['table'];

        // ✅ 全カラム取得し、除外する
        $allColumns = Schema::getColumnListing($table);
        $columns = array_values(array_diff($allColumns, $config['excluded']));

        // ✅ format_row に共通関数をセット
        $config['format_row'] = $formatRowHelper($columns, $config['master_maps'] ?? []);

        // ✅ データ取得 & 整形
        $data = $model::select($columns)->get();
        $rows = $data->map($config['format_row']);

        // ✅ ファイル名・ヘッダー設定
        $filename = "{$timestamp}_{$config['filename_prefix']}.csv";
        $headers = $config['headers'];

        // ✅ CSV出力処理
        $callback = function () use ($headers, $rows) {
            $stream = fopen('php://output', 'w');
            echo chr(0xEF) . chr(0xBB) . chr(0xBF); // UTF-8 BOM for Excel

            fputcsv($stream, $headers);
            foreach ($rows as $row) {
                fputcsv($stream, $row);
            }
            fclose($stream);
        };

        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
