<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:案件,契約・委任,請求・精算',
            'report_case_type' => 'nullable|string',
        ]);

        $reportType = $validated['report_type'];
        $caseType = $validated['report_case_type'];

        $consultationId = $request->input('consultation_id');
        $consultation = Consultation::with('client')->findOrFail($consultationId);
        $client = $consultation->client;

        // テンプレートファイル定義（暫定）
        $templateMap = [
            '案件' => [
                '交通事故' => '交通事故_案件シート_軽量化.xltx',
                '残業代' => '残業代請求_案件シート2.xltx',
            ],
            '契約・委任' => [
                '解雇' => '委任契約書セット(解雇)2.xltx',
                '残業代' => '委任契約書セット(残業代)2.xltx',
            ],
            '請求・精算' => [
                '請求書' => '請求書テンプレート.xltx',
                '精算書' => '精算書テンプレート.xltx',
            ],
        ];

        $templateFile = $templateMap[$reportType][$caseType] ?? null;
        if (!$templateFile) {
            return back()->withInput()->withErrors(['帳票テンプレートが定義されていません']);
        }

        $templatePath = storage_path("app/templates/{$templateFile}");
        if (!file_exists($templatePath)) {
            return back()->withInput()->withErrors(['テンプレートファイルが存在しません']);
        }

        try {
            $spreadsheet = IOFactory::load($templatePath);

            if ($reportType === '案件') {
                $sheet = $spreadsheet->getSheetByName('基本情報');

                $lastNameKana = mb_convert_kana($client->last_name_kana, 'CK');
                $firstNameKana = mb_convert_kana($client->first_name_kana, 'CK');
                $birthdayDate = optional($client->birthday)->toDateTime();
                $postalcode = '〒' . mb_convert_kana($client->address_postalcode, 'A');
                $address = $client->address_state . $client->address_city . $client->address_street;
                $cleanedName = trim(str_replace([' ', '　'], '', $client->name_kanji));

                switch ($caseType) {
                    case '交通事故':
                        $sheet->setCellValue('B2', $client->last_name_kanji);
                        $sheet->setCellValue('B3', $client->first_name_kanji);
                        $sheet->setCellValue('B4', $lastNameKana);
                        $sheet->setCellValue('B5', $firstNameKana);
                        if ($birthdayDate) {
                            $sheet->setCellValue('B6', Date::PHPToExcel($birthdayDate));
                            $sheet->getStyle('B6')->getNumberFormat()->setFormatCode('[$-ja-JP]ge.m.d');
                        }
                        $sheet->setCellValue('B7', $postalcode);
                        $sheet->setCellValue('B8', $address);
                        $sheet->setCellValue('B9', $client->address_name_kanji);
                        break;

                    case '残業代':
                        $sheet->setCellValue('B2', $cleanedName);
                        break;

                    default:
                        return back()->withInput()->withErrors(['未対応の案件種別です']);
                }
                } elseif ($reportType === '契約・委任') {
                    $sheet = $spreadsheet->getSheetByName('委任状');
                    if (!$sheet) {
                        return back()->withInput()->withErrors(['契約帳票：シート「委任状」が存在しません']);
                    }
                
                    $postalcode = mb_convert_kana($client->address_postalcode, 'A');
                    $address = $client->address_state . $client->address_city . $client->address_street;
                
                    $sheet->setCellValue('F4', $postalcode);
                    $sheet->setCellValue('F5', $address);
                    $sheet->setCellValue('F6', $client->name_kanji);
                } elseif ($reportType === '請求・精算') {
                
                    // ▼ オフィス設定（改行あり）
                    $officeId = $consultation->office_id;
                    $officeSettings = [
                        1 => [
                            'address' => "〒104-0061\n東京都中央区銀座６−３−９\n銀座高松ビル９階",
                            'bank'    => 'みずほ銀行　銀座支店（普通）XXXXXXXX　弁護士法人エース',
                        ],
                        2 => [
                            'address' => "〒231-0012\n神奈川県横浜市中区相生町2-42-3\n横浜エクセレント17-6階A",
                            'bank'    => '三菱UFJ銀行　横浜支店（普通）XXXXXXXX　弁護士法人エース',
                        ],
                        // 他事務所追加可
                    ];
                
                    $officeData = $officeSettings[$officeId] ?? [
                        'address' => '（住所未設定）',
                        'bank'    => '（口座未設定）',
                    ];
                
                    // ▼ 差し込みシート名（帳票種別による固定）
                    $sheetName = match ($caseType) {
                        '請求書' => '【請求書】',
                        '精算書' => '【明細書】',
                        default  => null,
                    };
                
                    if (!$sheetName || !$spreadsheet->getSheetByName($sheetName)) {
                        return back()->withInput()->withErrors(["請求・精算帳票：シート「{$sheetName}」が存在しません"]);
                    }
                
                    $sheet = $spreadsheet->getSheetByName($sheetName);
                
                    // ▼ 差し込み処理：共通
                    $sheet->setCellValue('A1', $client->name_kanji);
                    $sheet->setCellValue('J5', $officeData['address']);
                    $sheet->getStyle('J5')->getAlignment()->setWrapText(true);
                
                    // ▼ 差し込み処理：帳票別
                    switch ($caseType) {
                        case '請求書':
                            $sheet->setCellValue('B31', $officeData['bank']);
                            // 今後の請求書専用項目があればここに追記
                            break;
                        
                        case '精算書':
                            // 今後の精算書専用項目があればここに追記
                            break;
                        
                        default:
                            return back()->withInput()->withErrors(['未対応の請求・精算種別です']);
                    }
                } else {
                    return back()->withInput()->withErrors(['未対応の帳票種別です']);
                }

            // 表示ラベル取得
            $typeLabels = config('output_forms.report_types');
            $caseTypeLabels = match ($reportType) {
                '案件' => config('output_forms.case_report_types'),
                '契約・委任' => config('output_forms.contract_report_types'),
                '請求・精算' => config('output_forms.invoice_report_types'),
                default => [],
            };

            $reportTypeLabel = $typeLabels[$reportType] ?? $reportType;
            $caseTypeLabel = $caseTypeLabels[$caseType] ?? $caseType;

            // 出力ファイル名生成
            $filename = $reportTypeLabel . '_' . $caseTypeLabel . '_' . now()->format('YmdHi') . '.xlsx';

            $writer = new Xlsx($spreadsheet);
            return response()->streamDownload(function () use ($writer) {
                $writer->save('php://output');
            }, $filename);

        } catch (\Exception $e) {
            Log::error('帳票出力エラー', ['msg' => $e->getMessage()]);
            return back()->withInput()->withErrors(['帳票出力中にエラーが発生しました']);
        }
    }
}