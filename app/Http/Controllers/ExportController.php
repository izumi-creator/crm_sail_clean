<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
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
            'users' => [
                'model' => User::class,
                'table' => 'users',
                'filename_prefix' => 'users',
                'excluded' => ['email_verified_at', 'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at'],
                'headers' => [
                    'ID', 'ユーザID', '氏名',
                    'メールアドレス1', 
                    '作成日時', '更新日時',
                    '従業員区分', 'システム権限', '所属事務所',
                    '電話番号', '電話番号2', 'メールアドレス2',
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
