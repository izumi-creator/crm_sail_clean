<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Business;
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
    
        // 相談 or 受任案件 の判定
        if ($request->has('consultation_id')) {
            $consultation = Consultation::with('client')->findOrFail($request->input('consultation_id'));
        } elseif ($request->has('business_id')) {
            $consultation = Business::with('client')->findOrFail($request->input('business_id'));
        } else {
            return back()->withInput()->withErrors(['帳票出力対象が不明です']);
        }
    
        $client = $consultation->client;
        $officeId = $consultation->office_id;

        // テンプレートファイル定義（暫定）
        $templateMap = [
            '案件' => [
                '交通事故' => '案件1_交通事故_案件シート.xltx',
                '控訴' => '案件2_控訴審用案件シート.xltx',
                '債権' => '案件3_債権執行と開示請求.xltx',
                '残業代' => '案件4_残業代請求_案件シート.xltx',
                '訴訟（簡裁用）' => '案件5_訴訟案件シート（簡裁用）.xltx',
                '訴訟（地裁用）' => '案件6_訴訟案件シート（地裁用）.xltx',
                '訴訟（離婚）' => '案件7_訴訟案件シート（離婚）.xltx',
                '訴訟（婚姻費用分担）' => '案件8_調停案件シート (婚姻費用分担）.xltx',
                '訴訟（面会交流）' => '案件9_調停案件シート (面会交流）.xltx',
                '訴訟（養育費）' => '案件10_調停案件シート (養育費）.xltx',
                '訴訟（財産分与）' => '案件11_調停案件シート（財産分与）.xltx',
                '訴訟（夫婦関係調整）' => '案件12_調停案件シート（夫婦関係調整）.xltx',
                '労働審判' => '案件13_労働審判案件シート.xltx',
            ],
            '契約・委任' => [
                '解雇' => '契約1_委任契約書セット(解雇).xltx',
                '残業代' => '契約2_委任契約書セット(残業代).xltx',
                '養育費' => '契約3_委任契約書セット(養育費).xltx',
                '立退料' => '契約4_委任契約書セット(立退料).xltx',
                '不貞・請求する側' => '契約5_委任契約書セット(不貞・請求する側).xltx',
                '不貞・請求される側' => '契約6_委任契約書セット(不貞・請求される側).xltx',
                '離婚' => '契約7_委任契約書セット(離婚).xltx',
                '交通事故（弁特なし）' => '契約8_委任契約書セット(交通事故(弁特なし)).xltx',
                '交通事故（弁特なし・増額幅）' => '契約9_委任契約書セット(交通事故(弁特なし・増額幅)).xltx',
                '労災' => '契約10_委任契約書セット(労災).xltx',
                '時効援用' => '契約11_委任契約書セット(時効援用).xltx',
                '共有物分割' => '契約12_委任契約書セット(共有物分割).xltx',
                'その他一般' => '契約13_委任契約書セット(その他一般).xltx',
                '破産' => '契約14_委任契約書セット(破産).xltx',
                '個人再生' => '契約15_委任契約書セット(個人再生).xltx',
                'マンション管理費' => '契約16_委任契約書セット(マンション管理費).xltx',
                'Ｂ型肝炎' => '契約17_委任契約書セット(Ｂ型肝炎).xltx',
                'アスベスト' => '契約18_委任契約書セット(アスベスト).xltx',
                'タイムチャージ' => '契約19_委任契約書セット(タイムチャージ).xltx',
                '遺留分減殺請求' => '契約20_委任契約書セット(遺留分減殺請求).xltx',
                '相続放棄' => '契約21_委任契約書セット(相続放棄).xltx',
                '退職代行' => '契約22_委任契約書セット(退職代行).xltx',
                '認知のみ' => '契約23_委任契約書セット(認知のみ).xltx',
                '認知＋養育費' => '契約24_委任契約書セット(認知＋養育費).xltx',
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
                        break;

                    case '控訴':
                        break;
                    
                    case '債権':
                        break;

                    case '残業代':
                        $sheet->setCellValue('B2', $cleanedName);
                        break;
                    
                    case '訴訟（簡裁用）':
                        break;

                    case '訴訟（地裁用）':
                        break;

                    case '訴訟（離婚）':
                        $sheet->setCellValue('B10', $postalcode);
                        $sheet->setCellValue('B11', $address);
                        $sheet->setCellValue('B13', $client->name_kanji);
                        break;
                    
                    case '訴訟（婚姻費用分担）':
                        $sheet->setCellValue('B10', $postalcode);
                        $sheet->setCellValue('B11', $address);
                        $sheet->setCellValue('B13', $client->name_kanji);
                        $sheet->setCellValue('B14', $client->name_kana);
                        if ($birthdayDate) {
                            $sheet->setCellValue('B15', Date::PHPToExcel($birthdayDate));
                            $sheet->getStyle('B15')->getNumberFormat()->setFormatCode('[$-ja-JP]ge.m.d');
                        }
                        break;

                    case '訴訟（面会交流）':
                        $sheet->setCellValue('B10', $postalcode);
                        $sheet->setCellValue('B11', $address);
                        $sheet->setCellValue('B13', $client->name_kanji);
                        $sheet->setCellValue('B14', $client->name_kana);
                        if ($birthdayDate) {
                            $sheet->setCellValue('B15', Date::PHPToExcel($birthdayDate));
                            $sheet->getStyle('B15')->getNumberFormat()->setFormatCode('[$-ja-JP]ge.m.d');
                        }
                        break;

                    case '訴訟（養育費）':
                        $sheet->setCellValue('B10', $postalcode);
                        $sheet->setCellValue('B11', $address);
                        $sheet->setCellValue('B13', $client->name_kanji);
                        $sheet->setCellValue('B14', $client->name_kana);
                        if ($birthdayDate) {
                            $sheet->setCellValue('B15', Date::PHPToExcel($birthdayDate));
                            $sheet->getStyle('B15')->getNumberFormat()->setFormatCode('[$-ja-JP]ge.m.d');
                        }
                        break;
                    
                    case '訴訟（財産分与）':
                        $sheet->setCellValue('B10', $postalcode);
                        $sheet->setCellValue('B11', $address);
                        $sheet->setCellValue('B13', $client->name_kanji);
                        $sheet->setCellValue('B14', $client->name_kana);
                        if ($birthdayDate) {
                            $sheet->setCellValue('B15', Date::PHPToExcel($birthdayDate));
                            $sheet->getStyle('B15')->getNumberFormat()->setFormatCode('[$-ja-JP]ge.m.d');
                        }
                        break;

                    case '訴訟（夫婦関係調整）':
                        $sheet->setCellValue('B10', $postalcode);
                        $sheet->setCellValue('B11', $address);
                        $sheet->setCellValue('B13', $client->name_kanji);
                        $sheet->setCellValue('B14', $client->name_kana);
                        if ($birthdayDate) {
                            $sheet->setCellValue('B15', Date::PHPToExcel($birthdayDate));
                            $sheet->getStyle('B15')->getNumberFormat()->setFormatCode('[$-ja-JP]ge.m.d');
                        }
                        break;
                    
                    case '労働審判':
                        $sheet->setCellValue('B10', $postalcode);
                        $sheet->setCellValue('B11', $address);
                        $sheet->setCellValue('B13', $client->name_kanji);
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
                            'bank'    => 'みずほ銀行　銀座支店（普通）4104830　弁護士法人エース',
                        ],
                        2 => [
                            'address' => "〒231-0012\n神奈川県横浜市中区相生町2-42-3\n横浜エクセレント17-6階A",
                            'bank'    => '三菱UFJ銀行　横浜支店（普通）4723439　弁護士法人エース',
                        ],
                        3 => [
                            'address' => "〒248-0012\n神奈川県鎌倉市御成町12-10\n鎌倉ニュービルディング4階",
                            'bank'    => '三菱UFJ銀行　鎌倉支店（普通）0292903　弁護士法人エース',
                        ],
                        4 => [
                            'address' => "〒274-0825\n千葉県船橋市前原西2-13-10\n自然センタービル津田沼5階C号室",
                            'bank'    => '千葉銀行　津田沼駅前支店　普通預金　４２３３２５９　弁護士法人エース',
                        ],
                        5 => [
                            'address' => "〒430-0946\n静岡県浜松市中央区元城町219-21\n浜松元城町第一ビルディング702",
                            'bank'    => '静岡銀行　浜松中央支店（普通）0469502　弁護士法人エース',
                        ],                      
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