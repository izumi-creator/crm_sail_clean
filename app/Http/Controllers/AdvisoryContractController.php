<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Client;
use App\Models\AdvisoryContract;
use App\Models\User;
use App\Models\Task;
use App\Models\Negotiation;
use App\Models\RelatedParty;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Services\SlackBotNotificationService;
use Illuminate\Support\Facades\Auth;

class AdvisoryContractController extends Controller
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

    // 顧問契約一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = AdvisoryContract::query();

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

        $advisories = $query->with('client')->paginate(15);
        return view('advisory.index', compact('advisories'));
    }

    // 顧問契約登録画面
    public function create()
    {
        return view('advisory.create');
    }

    // 顧問契約登録処理
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
            'advisory_party' => 'required|in:' . implode(',', array_keys(config('master.advisory_parties'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.advisory_contracts_statuses'))),
            'explanation' => 'nullable|string|max:10000',
            'special_notes' => 'nullable|string|max:10000',
            'advisory_start_date' => 'nullable|date',
            'advisory_end_date' => 'nullable|date',
            'amount_monthly' => 'nullable|numeric',
            'contract_term_monthly' => 'nullable|numeric',
            'consultation_firstdate' => 'nullable|date',
            'payment_category' => 'nullable|in:' . implode(',', array_keys(config('master.payment_categories'))),
            'adviser_fee_auto' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:' . implode(',', array_keys(config('master.payment_methods'))),
            'withdrawal_request_amount' => 'nullable|numeric',
            'withdrawal_breakdown' => 'nullable|string|max:255',
            'withdrawal_update_date' => 'nullable|date',
            'office_id' => 'nullable|in:' . implode(',', array_keys(config('master.offices_id'))),
            'lawyer_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'nullable|exists:users,id',
            'source' => 'required|in:' . implode(',', array_keys(config('master.routes'))),
            'source_detail' => 'nullable|in:' . implode(',', array_keys(config('master.routedetails'))),
            'introducer_others' => 'nullable|string|max:255',
            'gift' => 'nullable|in:' . implode(',', array_keys(config('master.gifts'))),
            'newyearscard' => 'nullable|in:' . implode(',', array_keys(config('master.newyearscards'))),
        ]);

        // ▼契約期間（月）を自動算出
        if (!empty($validated['advisory_start_date']) && !empty($validated['advisory_end_date'])) {
            $start = new \DateTime($validated['advisory_start_date']);
            $end = new \DateTime($validated['advisory_end_date']);
            if ($start <= $end) {
                $interval = $start->diff($end);
                $validated['contract_term_monthly'] = ($interval->y * 12 + $interval->m + 1);
            }
        }

        // ▼ 顧問契約を作成
        $advisory = AdvisoryContract::create([
            'client_id' => $validated['client_id'],
            'advisory_party' => $validated['advisory_party'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'explanation' => $validated['explanation'],
            'special_notes' => $validated['special_notes'],
            'advisory_start_date' => $validated['advisory_start_date'],
            'advisory_end_date' => $validated['advisory_end_date'],
            'amount_monthly' => $validated['amount_monthly'],
            'contract_term_monthly' => $validated['contract_term_monthly'],
            'consultation_firstdate' => $validated['consultation_firstdate'],
            'payment_category' => $validated['payment_category'],
            'adviser_fee_auto' => $validated['adviser_fee_auto'],
            'payment_method' => $validated['payment_method'],
            'withdrawal_request_amount' => $validated['withdrawal_request_amount'],
            'withdrawal_breakdown' => $validated['withdrawal_breakdown'],
            'withdrawal_update_date' => $validated['withdrawal_update_date'],
            'office_id' => $validated['office_id'],
            'lawyer_id' => $validated['lawyer_id'],
            'paralegal_id' => $validated['paralegal_id'],
            'source' => $validated['source'],
            'source_detail' => $validated['source_detail'] ?? null,
            'introducer_others' => $validated['introducer_others'] ?? null,
            'gift' => $validated['gift'],
            'newyearscard' => $validated['newyearscard'],
        ]);

        // ✅ Slack通知送信
        $creatorName = optional($advisory->createdByUser)->name;
        $url = route('advisory.show', ['advisory' => $advisory->id]);
        $message = "顧問契約を追加しました。";

        // Botによる個別通知
        $notifiedUsers = collect([
            $advisory->lawyer,
            $advisory->paralegal,
        ])->filter();

        $slackBot = app(SlackBotNotificationService::class);
        foreach ($notifiedUsers as $user) {
            if (!empty($user->slack_channel_id)) {
                $slackBot->sendMessage("📝 {$message}\n顧問契約の件名：{$advisory->title}\n登録者：{$creatorName}\n🔗 URL：{$url}", $user->slack_channel_id);
            }
        }

        if ($request->filled('redirect_url')) {
        return redirect($request->input('redirect_url'))->with('success', '顧問契約を作成しました！');
        }

        return redirect()->route('advisory.index')->with('success', '顧問契約を作成しました！');
    }

    // 顧問契約詳細処理
    public function show(AdvisoryContract $advisory)
    {
        // 関連データをロード
        $advisory->load([
            'client',
            'lawyer',
            'lawyer2',
            'lawyer3',
            'paralegal',
            'paralegal2',
            'paralegal3',
            'advisoryConsultation',
            'tasks',
            'negotiations',
        ]);

        // クライアント情報（スペース除去した比較用文字列）
        $clientNameKanji = preg_replace('/\s/u', '', $advisory->client->name_kanji ?? '');
        $clientNameKana  = preg_replace('/\s/u', '', $advisory->client->name_kana ?? '');

        $responsibleKanji = preg_replace('/\s/u', '', 
            ($advisory->client->contact_last_name_kanji ?? '') . ($advisory->client->contact_first_name_kanji ?? '')
        );
        $responsibleKana = preg_replace('/\s/u', '', 
            ($advisory->client->contact_last_name_kana ?? '') . ($advisory->client->contact_first_name_kana ?? '')
        );

        // クライアント一致検索（自分以外）
        $matchedClients = Client::where('id', '!=', $advisory->client_id)
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

        return view('advisory.show', compact(
            'advisory',
            'matchedClients',
            'matchedRelatedParties'
        ));
    }


    // 顧問契約編集画面
    public function update(Request $request, AdvisoryContract $advisory)
    {

        $before_status = $advisory->status;


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
            'advisory_party' => 'required|in:' . implode(',', array_keys(config('master.advisory_parties'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.advisory_contracts_statuses'))),
            'explanation' => 'nullable|string|max:10000',
            'special_notes' => 'nullable|string|max:10000',
            'advisory_start_date' => 'nullable|date',
            'advisory_end_date' => 'nullable|date',
            'amount_monthly' => 'nullable|numeric',
            'contract_term_monthly' => 'nullable|numeric',
            'consultation_firstdate' => 'nullable|date',
            'payment_category' => 'nullable|in:' . implode(',', array_keys(config('master.payment_categories'))),
            'adviser_fee_auto' => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:' . implode(',', array_keys(config('master.payment_methods'))),
            'withdrawal_request_amount' => 'nullable|numeric',
            'withdrawal_breakdown' => 'nullable|string|max:255',
            'withdrawal_update_date' => 'nullable|date',
            'office_id' => 'nullable|in:' . implode(',', array_keys(config('master.offices_id'))),
            'lawyer_id' => 'nullable|exists:users,id',
            'lawyer2_id' => 'nullable|exists:users,id',
            'lawyer3_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'nullable|exists:users,id',
            'paralegal2_id' => 'nullable|exists:users,id',
            'paralegal3_id' => 'nullable|exists:users,id',
            'source' => 'required|in:' . implode(',', array_keys(config('master.routes'))),
            'source_detail' => 'nullable|in:' . implode(',', array_keys(config('master.routedetails'))),
            'introducer_others' => 'nullable|string|max:255',
            'gift' => 'nullable|in:' . implode(',', array_keys(config('master.gifts'))),
            'newyearscard' => 'nullable|in:' . implode(',', array_keys(config('master.newyearscards'))),
        ]);

        // ✳ ステータスに応じた追加チェック
        $validator->after(function ($validator) use ($request) {


            if (in_array((int)$request->status, [2, 3, 4, 5, 6])) {
                if (empty($request->explanation)) {
                    $validator->errors()->add('explanation', '「説明」を入力してください。');
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
                if (empty($request->source)) {
                    $validator->errors()->add('source', '「ソース」を選択してください。');
                }
                if (empty($request->source_detail)) {
                    $validator->errors()->add('source_detail', '「ソース詳細」を選択してください。');
                }
            }

            if (in_array((int)$request->status, [3, 5, 6])) {
                if ((int)$request->opponent_confliction !== 1) {
                    $validator->errors()->add('opponent_confliction', '「利益相反確認」が「問題なし」以外です。');
                }
                if (empty($request->advisory_start_date)) {
                    $validator->errors()->add('advisory_start_date', '「契約開始日」を入力してください。');
                }
                if (empty($request->advisory_end_date)) {
                    $validator->errors()->add('advisory_end_date', '「契約終了日」を入力してください。');
                }
                if (empty($request->amount_monthly)) {
                    $validator->errors()->add('amount_monthly', '「顧問料月額」を入力してください。');
                }
                if (empty($request->payment_category)) {
                    $validator->errors()->add('payment_category', '「支払区分」を選択してください。');
                }
                if (empty($request->payment_method)) {
                    $validator->errors()->add('payment_method', '「支払方法」を選択してください。');
                }
                if (empty($request->withdrawal_request_amount)) {
                    $validator->errors()->add('withdrawal_request_amount', '「引落依頼額」を入力してください。');
                }
                if (empty($request->withdrawal_breakdown)) {
                    $validator->errors()->add('withdrawal_breakdown', '「引落内訳」を入力してください。');
                }
            }

        });

        $validated = $validator->validate();

        // ▼契約期間（月）を自動算出
        if (!empty($validated['advisory_start_date']) && !empty($validated['advisory_end_date'])) {
            $start = new \DateTime($validated['advisory_start_date']);
            $end = new \DateTime($validated['advisory_end_date']);
            if ($start <= $end) {
                $interval = $start->diff($end);
                $validated['contract_term_monthly'] = ($interval->y * 12 + $interval->m + 1);
            }
        }

        $advisory->update([
            'client_id' => $validated['client_id'],
            'advisory_party' => $validated['advisory_party'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'explanation' => $validated['explanation'],
            'special_notes' => $validated['special_notes'],
            'advisory_start_date' => $validated['advisory_start_date'],
            'advisory_end_date' => $validated['advisory_end_date'],
            'amount_monthly' => $validated['amount_monthly'],
            'contract_term_monthly' => $validated['contract_term_monthly'],
            'consultation_firstdate' => $validated['consultation_firstdate'],
            'payment_category' => $validated['payment_category'],
            'adviser_fee_auto' => $validated['adviser_fee_auto'],
            'payment_method' => $validated['payment_method'],
            'withdrawal_request_amount' => $validated['withdrawal_request_amount'],
            'withdrawal_breakdown' => $validated['withdrawal_breakdown'],
            'withdrawal_update_date' => $validated['withdrawal_update_date'],
            'office_id' => $validated['office_id'],
            'lawyer_id' => $validated['lawyer_id'],
            'lawyer2_id' => $validated['lawyer2_id'],
            'lawyer3_id' => $validated['lawyer3_id'],
            'paralegal_id' => $validated['paralegal_id'],
            'paralegal2_id' => $validated['paralegal2_id'],
            'paralegal3_id' => $validated['paralegal3_id'],
            'source' => $validated['source'],
            'source_detail' => $validated['source_detail'] ?? null,
            'introducer_others' => $validated['introducer_others'] ?? null,
            'gift' => $validated['gift'],
            'newyearscard' => $validated['newyearscard'],
        ]);

        $notificationMessage = null; // ← Slackメッセージ

        $before_status = (int) $before_status;
        $after_status = (int) $validated['status'];

        if ($before_status !== $after_status) {
            $statusLabels = config('master.advisory_contracts_statuses');
        
            $beforeLabel = $statusLabels[$before_status] ?? "不明（$before_status）";
            $afterLabel = $statusLabels[$after_status] ?? "不明（$after_status）";
            $updaterName = optional($advisory->updatedByUser)->name ?? '不明';
            $url = route('advisory.show', ['advisory' => $advisory->id]);

            $notificationMessage = "📌顧問契約のステータスが変更されました\n"
                . "■ 件名：{$advisory->title}\n"
                . "■ ステータス：{$beforeLabel} → {$afterLabel}\n"
                . "■ 更新者：{$updaterName}\n"
                . "🔗 URL：{$url}";

        // Slack送信処理（あれば）
        if ($notificationMessage) {
            $notifiedUsers = collect([
                $advisory->lawyer,
                $advisory->lawyer2,
                $advisory->lawyer3,
                $advisory->paralegal,
                $advisory->paralegal2,
                $advisory->paralegal3,
            ])->filter();

            $slackBot = app(SlackBotNotificationService::class);
            foreach ($notifiedUsers as $user) {
                if (!empty($user->slack_channel_id)) {
                    $slackBot->sendMessage($notificationMessage, $user->slack_channel_id);
                }
            }

        }
        }
        
        return redirect()->route('advisory.show', $advisory->id)->with('success', '顧問契約が更新されました。');
    }

    // 顧問契約削除処理
    public function destroy(AdvisoryContract $advisory)
    {
        $this->ensureIsAdmin();

        try {

            $title = $advisory->title;

            $advisory->delete();

            // ✅ Slack通知送信
            $userName = Auth::user()?->name ?? '不明';
            $message = "🗑️ 顧問契約を削除しました！\n顧問契約の件名：{$title}\n削除者：{$userName}";

            $notifiedUsers = collect([
                $advisory->lawyer,
                $advisory->lawyer2,
                $advisory->lawyer3,
                $advisory->paralegal,
                $advisory->paralegal2,
                $advisory->paralegal3,
            ])->filter();

            $slackBot = app(SlackBotNotificationService::class);
            foreach ($notifiedUsers as $user) {
                if (!empty($user->slack_channel_id)) {
                    $slackBot->sendMessage($message, $user->slack_channel_id);
                }
            }

            return redirect()->route('advisory.index')->with('success', '顧問契約を削除しました');
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
    public function conflictUpdate(Request $request, AdvisoryContract $advisory)
    {
        // バリデーション（利益相反確認は必須）
        $validated = $request->validate([
            'opponent_confliction' => 'required|in:1,2,3',
        ], [
            'opponent_confliction.required' => '「利益相反確認結果」は必須です。',
            'opponent_confliction.in' => '選択された「利益相反確認結果」が不正です。',
        ]);
    
        // 更新処理（date型なので today() を使用）
        $advisory->update([
            'opponent_confliction' => $validated['opponent_confliction'],
            'opponent_confliction_date' => \Carbon\Carbon::today(),
        ]);
    
        return redirect()
            ->route('advisory.show', $advisory->id)
            ->with('success', '利益相反チェック結果を更新しました。');
    }

    /** 顧問契約検索API */
    public function search(Request $request)
    {
        $keyword = $request->input('q');
    
        $results = [];
    
        if ($keyword) {
            $results = AdvisoryContract::where('title', 'like', "%{$keyword}%")
                ->select('id', 'title')
                ->limit(10)
                ->get()
                ->map(fn($advisory) => ['id' => $advisory->id, 'text' => $advisory->title]);
        }
    
        return response()->json(['results' => $results]);
    }

}
