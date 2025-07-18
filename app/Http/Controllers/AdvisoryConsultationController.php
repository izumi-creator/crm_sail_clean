<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Client;
use App\Models\AdvisoryConsultation;
use App\Models\AdvisoryContract;
use App\Models\User;
use App\Models\Consultation;
use App\Models\RelatedParty;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Services\SlackBotNotificationService;
use Illuminate\Support\Facades\Auth;

class AdvisoryConsultationController extends Controller
{

    /**
     * 管理者権限チェック
     */
    private function ensureIsAdmin()
    {
        $loginUser = auth()->user();
        if ($loginUser->role_type != 1) {
            abort(403, '管理者権限が必要です。');
        }
    }

    // 顧問相談一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = AdvisoryConsultation::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('advisory_party')) {
            $query->where('advisory_party', $request->advisory_party);
        }
        // クライアント名（漢字orかな）で検索
        if ($request->filled('client_name')) {
            $clientIds = Client::where(function ($query) use ($request) {
                $query->where('name_kanji', 'like', '%' . $request->client_name . '%')
                      ->orWhere('name_kana', 'like', '%' . $request->client_name . '%');
            })->pluck('id');
        
            $query->whereIn('client_id', $clientIds);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $advisory_consultations = $query->with('client')->paginate(15);
        return view('advisory_consultation.index', compact('advisory_consultations'));
    }

    // 顧問相談登録画面
    public function create()
    {
        return view('advisory_consultation.create');
    }

    // 顧問相談登録処理
    public function store(Request $request)
    {

        // ▼ Select2の初期テキスト表示対応（クライアント）
        if ($request->has('client_id')) {
            $client = Client::find($request->input('client_id'));
            if ($client) {
                $request->merge([
                    'client_name_display' => $client->name_kanji,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（弁護士）
        if ($request->has('lawyer_id')) {
            $lawyer = User::find($request->input('lawyer_id'));
            if ($lawyer) {
                $request->merge([
                    'lawyer_name_display' => $lawyer->name,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（パラリーガル）
        if ($request->has('paralegal_id')) {
            $paralegal = User::find($request->input('paralegal_id'));
            if ($paralegal) {
                $request->merge([
                    'paralegal_name_display' => $paralegal->name,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（顧問契約）
        if ($request->has('advisory_contract_id')) {
            $advisoryContract = AdvisoryContract::find($request->input('advisory_contract_id'));
            if ($advisoryContract) {
                $request->merge([
                    'advisory_contract_name_display' => $advisoryContract->name,
                ]);
            }
        }

        // ▼ クライアントから client_type を取得し advisory_party に設定
        if ($request->filled('client_id')) {
            $client = Client::find($request->input('client_id'));
            if ($client) {
                // バリデーションの前に advisory_party をマージ
                $request->merge([
                    'advisory_party' => $client->client_type,
                    'client_name_display' => $client->name_kanji,
                ]);
            }
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'advisory_contract_id' => 'required|exists:advisory_contracts,id',
            'advisory_party' => 'required|in:' . implode(',', array_keys(config('master.advisory_parties'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.advisory_consultations_statuses'))),
            'case_summary' => 'nullable|string|max:10000',
            'special_notes' => 'nullable|string|max:10000',
            'consultation_start_date' => 'nullable|date',
            'office_id' => 'nullable|in:' . implode(',', array_keys(config('master.offices_id'))),
            'lawyer_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'nullable|exists:users,id',
            'source' => 'nullable|in:' . implode(',', array_keys(config('master.routes'))),
            'source_detail' => 'nullable|in:' . implode(',', array_keys(config('master.routedetails'))),
            'introducer_others' => 'nullable|string|max:255',
            'gift' => 'nullable|in:' . implode(',', array_keys(config('master.gifts'))),
            'newyearscard' => 'nullable|in:' . implode(',', array_keys(config('master.newyearscards'))),
        ]);

        // カスタムチェック
        $exists = AdvisoryContract::where('id', $request->advisory_contract_id)
            ->where('client_id', $request->client_id)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'advisory_contract_id' => '選択された顧問契約はこのクライアントに属していません。',
            ]);
        }

        $advisory_consultation = AdvisoryConsultation::create([
            'client_id' => $validated['client_id'],
            'advisory_contract_id' => $validated['advisory_contract_id'],
            'advisory_party' => $validated['advisory_party'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'case_summary' => $validated['case_summary'] ?? null,
            'special_notes' => $validated['special_notes'] ?? null,
            'consultation_start_date' => $validated['consultation_start_date'] ?? null,
            'office_id' => $validated['office_id'] ?? null,
            'lawyer_id' => $validated['lawyer_id'] ?? null,
            'paralegal_id' => $validated['paralegal_id'] ?? null,
            'source' => $validated['source'] ?? null,
            'source_detail' => $validated['source_detail'] ?? null,
            'introducer_others' => $validated['introducer_others'] ?? null,
            'gift' => $validated['gift'] ?? null,
            'newyearscard' => $validated['newyearscard'] ?? null,
        ]);

        // ✅ Slack通知送信
        $creatorName = optional($advisory_consultation->createdByUser)->name;
        $url = route('advisory_consultation.show', ['advisory_consultation' => $advisory_consultation->id]);
        $message = "顧問相談を追加しました。";

        // Botによる個別通知
        $notifiedUsers = collect([
            $advisory_consultation->lawyer,
            $advisory_consultation->paralegal,
        ])->filter();

        $slackBot = app(SlackBotNotificationService::class);
        foreach ($notifiedUsers as $user) {
            if (!empty($user->slack_channel_id)) {
                $slackBot->sendMessage("📝 {$message}\n顧問相談の件名：{$advisory_consultation->title}\n登録者：{$creatorName}\n🔗 URL：{$url}", $user->slack_channel_id);
            }
        }        

        if ($request->filled('redirect_url')) {
        return redirect($request->input('redirect_url'))->with('success', '顧問相談を登録しました！');
        }

        return redirect()->route('advisory_consultation.index')->with('success', '顧問相談を登録しました！');
    }

    // 顧問相談詳細処理
    public function show(AdvisoryConsultation $advisory_consultation)
    {
        // 関連データをロード
        $advisory_consultation->load([
            'client',
            'lawyer',
            'lawyer2',
            'lawyer3',
            'paralegal',
            'paralegal2',
            'paralegal3',
            'advisoryContract',
            'consultation',
            'relatedParties',
            'tasks',
            'negotiations',
        ]);

        // クライアント情報（スペース除去した比較用文字列）
        $clientNameKanji = preg_replace('/\s/u', '', $advisory_consultation->client->name_kanji ?? '');
        $clientNameKana  = preg_replace('/\s/u', '', $advisory_consultation->client->name_kana ?? '');

        $responsibleKanji = preg_replace('/\s/u', '', 
            ($advisory_consultation->client->contact_last_name_kanji ?? '') . ($advisory_consultation->client->contact_first_name_kanji ?? '')
        );
        $responsibleKana = preg_replace('/\s/u', '', 
            ($advisory_consultation->client->contact_last_name_kana ?? '') . ($advisory_consultation->client->contact_first_name_kana ?? '')
        );

        // クライアント一致検索（自分以外）
        $matchedClients = Client::where('id', '!=', $advisory_consultation->client_id)
            ->where(function ($query) use ($clientNameKanji, $clientNameKana, $responsibleKanji, $responsibleKana) {
                $query->whereRaw("REPLACE(REPLACE(name_kanji, ' ', ''), '　', '') = ?", [$clientNameKanji])
                      ->orWhereRaw("REPLACE(REPLACE(name_kana, ' ', ''), '　', '') = ?", [$clientNameKana])
                      ->orWhereRaw("REPLACE(REPLACE(CONCAT(contact_last_name_kanji, contact_first_name_kanji), ' ', ''), '　', '') = ?", [$clientNameKanji])
                      ->orWhereRaw("REPLACE(REPLACE(CONCAT(contact_last_name_kana, contact_first_name_kana), ' ', ''), '　', '') = ?", [$clientNameKana])
                      ->orWhereRaw("REPLACE(REPLACE(CONCAT(contact_last_name_kanji, contact_first_name_kanji), ' ', ''), '　', '') = ?", [$responsibleKanji])
                      ->orWhereRaw("REPLACE(REPLACE(CONCAT(contact_last_name_kana, contact_first_name_kana), ' ', ''), '　', '') = ?", [$responsibleKana]);
            })
            ->get();

        // 関係者一致検索
        $matchedRelatedParties = RelatedParty::where(function ($query) use ($clientNameKanji, $clientNameKana, $responsibleKanji, $responsibleKana) {
            $query->whereRaw("REPLACE(REPLACE(relatedparties_name_kanji, ' ', ''), '　', '') = ?", [$clientNameKanji])
                  ->orWhereRaw("REPLACE(REPLACE(relatedparties_name_kana, ' ', ''), '　', '') = ?", [$clientNameKana])
                  ->orWhereRaw("REPLACE(REPLACE(manager_name_kanji, ' ', ''), '　', '') = ?", [$clientNameKanji])
                  ->orWhereRaw("REPLACE(REPLACE(manager_name_kana, ' ', ''), '　', '') = ?", [$clientNameKana])
                  ->orWhereRaw("REPLACE(REPLACE(manager_name_kanji, ' ', ''), '　', '') = ?", [$responsibleKanji])
                  ->orWhereRaw("REPLACE(REPLACE(manager_name_kana, ' ', ''), '　', '') = ?", [$responsibleKana]);
        })->get();

        return view('advisory_consultation.show', compact(
            'advisory_consultation',
            'matchedClients',
            'matchedRelatedParties'
        ));
    }

    // 顧問相談編集画面
    public function update(Request $request, AdvisoryConsultation $advisory_consultation)
    {

        $before_status = $advisory_consultation->status;

        // ▼ クライアントから client_type を取得し advisory_party に設定
        if ($request->filled('client_id')) {
            $client = Client::find($request->input('client_id'));
            if ($client) {
                // バリデーションの前に advisory_party をマージ
                $request->merge([
                    'advisory_party' => $client->client_type,
                    'client_name_display' => $client->name_kanji,
                ]);
            }
        }

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'advisory_contract_id' => 'required|exists:advisory_contracts,id',
            'advisory_party' => 'required|in:' . implode(',', array_keys(config('master.advisory_parties'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.advisory_consultations_statuses'))),
            'case_summary' => 'nullable|string|max:10000',
            'special_notes' => 'nullable|string|max:10000',
            'consultation_start_date' => 'nullable|date',
            'consultation_end_date' => 'nullable|date',
            'close_reason' => 'nullable|in:' . implode(',', array_keys(config('master.close_reasons'))),
            'office_id' => 'nullable|in:' . implode(',', array_keys(config('master.offices_id'))),
            'lawyer_id' => 'nullable|exists:users,id',
            'lawyer2_id' => 'nullable|exists:users,id',
            'lawyer3_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'nullable|exists:users,id',
            'paralegal2_id' => 'nullable|exists:users,id',
            'paralegal3_id' => 'nullable|exists:users,id',
        ]);

        // カスタムチェック
        $exists = AdvisoryContract::where('id', $request->advisory_contract_id)
            ->where('client_id', $request->client_id)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'advisory_contract_id' => '選択された顧問契約はこのクライアントに属していません。',
            ]);
        }

        // ✳ ステータスに応じた追加チェック
        $validator->after(function ($validator) use ($request) {

            if (in_array((int)$request->status, [2, 3, 4])) {
                if ((int)$request->opponent_confliction !== 1) {
                    $validator->errors()->add('opponent_confliction', '「利益相反確認」が「問題なし」以外です。');
                }
                if (empty($request->case_summary)) {
                    $validator->errors()->add('case_summary', '「相談概要」を入力してください。');
                }
                if (empty($request->consultation_start_date)) {
                    $validator->errors()->add('consultation_start_date', '「相談開始日」を入力してください。');
                }
                if (empty($request->office_id)) {
                    $validator->errors()->add('office_id', '「取扱事務所」を選択してください。');
                }
                if (empty($request->lawyer_id)) {
                    $validator->errors()->add('lawyer_id', '「担当弁護士」を選択してください。');
                }
                if (empty($request->paralegal_id)) {
                    $validator->errors()->add('paralegal_id', '「担当パラリーガル」を選択してください。');
                }
            }

            if (in_array((int)$request->status, [3, 4])) {
                if (empty($request->consultation_end_date)) {
                    $validator->errors()->add('consultation_end_date', '「相談終了日」を入力してください。');
                }
                if (empty($request->close_reason)) {
                    $validator->errors()->add('close_reason', '「解決理由」を選択してください。');
                }
            }

            if ((int)$request->status === 4) {
                if ((int)$request->close_reason !== 3) {
                    $validator->errors()->add('close_reason', '「解決理由」は「相談（受任案件）へ移行」を選択してください。');
                }
            }
        });

        $validated = $validator->validate();

        $advisory_consultation->update([
            'client_id' => $validated['client_id'],
            'advisory_contract_id' => $validated['advisory_contract_id'],
            'advisory_party' => $validated['advisory_party'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'case_summary' => $validated['case_summary'],
            'special_notes' => $validated['special_notes'],
            'consultation_start_date' => $validated['consultation_start_date'],
            'consultation_end_date' => $validated['consultation_end_date'],
            'close_reason' => $validated['close_reason'],
            'office_id' => $validated['office_id'],
            'lawyer_id' => $validated['lawyer_id'],
            'lawyer2_id' => $validated['lawyer2_id'],
            'lawyer3_id' => $validated['lawyer3_id'],
            'paralegal_id' => $validated['paralegal_id'],
            'paralegal2_id' => $validated['paralegal2_id'],
            'paralegal3_id' => $validated['paralegal3_id'],
        ]);

        $messages = ['顧問相談が更新されました。'];
        $notificationMessage = null; // ← Slackメッセージ
        
        $before_status = (int) $before_status;
        $after_status = (int) $validated['status'];


        if ($before_status !== $after_status) {
            $statusLabels = config('master.advisory_consultations_statuses');

            $beforeLabel = $statusLabels[$before_status] ?? "不明（$before_status）";
            $afterLabel = $statusLabels[$after_status] ?? "不明（$after_status）";        
            $updaterName = optional($advisory_consultation->updatedByUser)->name ?? '不明';
            $url = route('advisory_consultation.show', ['advisory_consultation' => $advisory_consultation->id]);
        
            $notificationMessage = "📌顧問相談ステータスが変更されました\n"
                . "■ 件名：{$advisory_consultation->title}\n"
                . "■ ステータス：{$beforeLabel} → {$afterLabel}\n"
                . "■ 更新者：{$updaterName}\n"
                . "🔗 URL：{$url}";


            // ステータスが「4：相談へ移行」の場合は相談作成メッセージ追加
            if ($before_status !== 4 && $after_status === 4) {
                $consultation = $this->migrateToConsultation($advisory_consultation);

                if ($consultation->wasRecentlyCreated) {
                    $messages[] = "▶ 相談が新規作成されました（相談ID: #{$consultation->id}）。";
                    $notificationMessage .= "\n▶ 相談が新規作成されました（案件ID: #{$consultation->id}）";

                    $count = RelatedParty::where('consultation_id', $consultation->id)->count();
                    if ($count > 0) {
                        $messages[] = "▶ 関係者{$count}名に相談を自動設定しました。";
                        $notificationMessage .= "\n▶ 関係者{$count}名に相談を自動設定しました";
                    }
                } else {
                    $messages[] = "▶ 相談はすでに作成されています（相談ID: #{$consultation->id}）。";
                    $notificationMessage .= "\n▶ 相談はすでに作成されています（相談ID: #{$consultation->id}）";
                }
            }
        }

        // Slack送信処理（あれば）
        if ($notificationMessage) {
            $notifiedUsers = collect([
                $advisory_consultation->lawyer,
                $advisory_consultation->lawyer2,
                $advisory_consultation->lawyer3,
                $advisory_consultation->paralegal,
                $advisory_consultation->paralegal2,
                $advisory_consultation->paralegal3,
            ])->filter();

            $slackBot = app(SlackBotNotificationService::class);
            foreach ($notifiedUsers as $user) {
                if (!empty($user->slack_channel_id)) {
                    $slackBot->sendMessage($notificationMessage, $user->slack_channel_id);
                }
            }

        }

        return redirect()
            ->route('advisory_consultation.show', $advisory_consultation->id)
            ->with('success', implode("\n", $messages));
    }

    private function migrateToConsultation(AdvisoryConsultation $advisory_consultation)
    {

    $consultation = Consultation::firstOrCreate(
        ['advisory_consultation_id' => $advisory_consultation->id],
        [
            'client_id' => $advisory_consultation->client_id,
            'consultation_party' => $advisory_consultation->advisory_party,
            'status' => 1, // 初期ステータス
            'title' => $advisory_consultation->title,
            'case_summary' => $advisory_consultation->case_summary,
            'special_notes' => $advisory_consultation->special_notes,
            'inquirytype' => 4, // その他
            'consultationtype'  => 4, // その他
            'opponent_confliction' => 1, // 実施済
            'opponent_confliction_date' => $advisory_consultation->opponent_confliction_date,
            'office_id' => $advisory_consultation->office_id,
            'lawyer_id' => $advisory_consultation->lawyer_id,
            'paralegal_id' => $advisory_consultation->paralegal_id,
            'lawyer2_id' => $advisory_consultation->lawyer2_id,
            'paralegal2_id' => $advisory_consultation->paralegal2_id,
            'lawyer3_id' => $advisory_consultation->lawyer3_id,
            'paralegal3_id' => $advisory_consultation->paralegal3_id,
        ]
        );

        // 顧問相談に consultation_id を紐づけ（新規作成時のみ）
        $advisory_consultation->consultation_id = $consultation->id;
        $advisory_consultation->save();

        // 関係者に advisory_consultation_id を紐づけ（新規作成時のみ）
        if ($consultation->wasRecentlyCreated) {
            RelatedParty::where('advisory_consultation_id', $advisory_consultation->id)
                ->update(['consultation_id' => $consultation->id]);
        }

        return $consultation;
    }


    // 顧問相談削除処理
    public function destroy(AdvisoryConsultation $advisory_consultation)
    {
        $this->ensureIsAdmin();

        try {

            $title = $advisory_consultation->title;

            $advisory_consultation->delete();

            // ✅ Slack通知送信
            $userName = Auth::user()?->name ?? '不明';
            $message = "🗑️ 顧問相談を削除しました！\n顧問相談の件名：{$title}\n削除者：{$userName}";

            $notifiedUsers = collect([
                $advisory_consultation->lawyer,
                $advisory_consultation->lawyer2,
                $advisory_consultation->lawyer3,
                $advisory_consultation->paralegal,
                $advisory_consultation->paralegal2,
                $advisory_consultation->paralegal3,
            ])->filter();

            $slackBot = app(SlackBotNotificationService::class);
            foreach ($notifiedUsers as $user) {
                if (!empty($user->slack_channel_id)) {
                    $slackBot->sendMessage($message, $user->slack_channel_id);
                }
            }

            return redirect()->route('advisory_consultation.index')->with('success', '顧問相談を削除しました');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                return response()->view('errors.db_constraint', [
                    'message' => '関連データがあるため削除できません。'
                ], 500);
            }
        
            // 1451以外のエラーはLaravelの例外処理に投げる
            throw $e;
        }        
    }

    //利益相反更新処理
    public function conflictUpdate(Request $request, AdvisoryConsultation $advisory_consultation)
    {
        // バリデーション（利益相反確認は必須）
        $validated = $request->validate([
            'opponent_confliction' => 'required|in:1,2,3',
        ], [
            'opponent_confliction.required' => '「利益相反確認結果」は必須です。',
            'opponent_confliction.in' => '選択された「利益相反確認結果」が不正です。',
        ]);
    
        // 更新処理（date型なので today() を使用）
        $advisory_consultation->update([
            'opponent_confliction' => $validated['opponent_confliction'],
            'opponent_confliction_date' => \Carbon\Carbon::today(),
        ]);
    
        return redirect()
            ->route('advisory_consultation.show', $advisory_consultation->id)
            ->with('success', '利益相反チェック結果を更新しました。');
    }



    /** 顧問相談検索API */
    public function search(Request $request)
    {
        $keyword = $request->input('q');
    
        $results = [];
    
        if ($keyword) {
            $results = AdvisoryConsultation::where('title', 'like', "%{$keyword}%")
                ->select('id', 'title')
                ->limit(10)
                ->get()
                ->map(fn($advisoryConsultation) => ['id' => $advisoryConsultation->id, 'text' => $advisoryConsultation->title]);
        }
    
        return response()->json(['results' => $results]);
    }

}
