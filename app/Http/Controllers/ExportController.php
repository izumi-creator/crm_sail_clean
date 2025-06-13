<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\User;
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
                    '作成日時', '更新日時', 
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
                    '作成日時', '更新日時',
                ],
                'master_maps' => [
                    'employee_type' => 'employee_types',
                    'role_type'     => 'role_types',
                    'office_id'     => 'offices',
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
                    '備考', '作成日時', '更新日時',
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
                    '所在地', '電話番号', '備考', '作成日時', '更新日時',
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
                    '作成日時', '更新日時',
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
