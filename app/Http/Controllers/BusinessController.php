<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Consultation;
use App\Models\Client;
use App\Models\User;
use App\Models\RelatedParty;
use App\Models\CourtTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BusinessController extends Controller
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

    // 受任案件一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = Business::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        if ($request->filled('consultation_party')) {
            $query->where('consultation_party', $request->consultation_party);
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

        $businesses = $query->with('client')->paginate(15);
        return view('business.index', compact('businesses'));
    }

    // 受任案件追加画面
    public function create()
    {
        $this->ensureIsAdmin();
        return view('business.create');
    }

    // 受任案件追加処理
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
        // ▼ Select2の初期テキスト表示対応（相談）
        if ($request->has('consultation_id')) {
            $consultation = Consultation::find($request->input('consultation_id'));
            if ($consultation) {
                $request->merge([
                    'consultation_name_display' => $consultation->title,
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

        // ▼ クライアントから client_type を取得し consultation_party に設定
        if ($request->filled('client_id')) {
            $client = Client::find($request->input('client_id'));
            if ($client) {
                // バリデーションの前に consultation_party をマージ
                $request->merge([
                    'consultation_party' => $client->client_type,
                    'client_name_display' => $client->name_kanji,
                ]);
            }
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'consultation_id' => [
                'required',
                'exists:consultations,id',
                Rule::unique('businesses')->where(function ($query) use ($request) {
                    return $query->where('consultation_id', $request->consultation_id);
                }),
            ],
            'consultation_party' => 'required|in:' . implode(',', array_keys(config('master.consultation_parties'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.business_statuses'))),
            'case_summary' => 'required|string|max:1000',
            'special_notes' => 'nullable|string|max:1000',
            'inquirytype' => 'required|in:' . implode(',', array_keys(config('master.inquirytypes'))),
            'consultationtype' => 'required|in:' . implode(',', array_keys(config('master.consultation_types'))),
            'case_category' => 'required|in:' . implode(',', array_keys(config('master.case_categories'))),
            'case_subcategory' => 'required|in:' . implode(',', array_keys(config('master.case_subcategories'))),
            'appointment_date' => 'required|date',
            'status_limitday' => 'nullable|date',
            'office_id' => 'required|in:' . implode(',', array_keys(config('master.offices_id'))),
            'lawyer_id' => 'required|exists:users,id',
            'paralegal_id' => 'required|exists:users,id',
            'feefinish_prospect' => 'required|string|max:255',
            'feesystem' => 'required|string|max:255',
            'sales_prospect' => 'required|numeric',
            'feesystem_initialvalue' => 'required|numeric',
            'sales_reason_updated' => 'required|date',
            'enddate_prospect' => 'required|date',
            'enddate_prospect_initialvalue' => 'required|date',
            'deposit' => 'required|numeric',
            'performance_reward' => 'required|numeric',
            'difference' => 'required|numeric',
            'requestfee_initialvalue' => 'required|numeric',
            'requestfee' => 'required|numeric',
            'requestfee_balance' => 'required|numeric',
            'route' => 'required|in:' . implode(',', array_keys(config('master.routes'))),
            'routedetail' => 'required|in:' . implode(',', array_keys(config('master.routedetails'))),
            'introducer' => 'nullable|string|max:255',
            'introducer_others' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
            'progress_comment' => 'nullable|string|max:255',

        ]);

        // 差額の再計算
        $sales_prospect = $validated['sales_prospect'] ?? 0;
        $deposit = $validated['deposit'] ?? 0;
        $reward = $validated['performance_reward'] ?? 0;
        $validated['difference'] = $sales_prospect - $deposit - $reward;

        // 預り金残の再計算
        $initial = $validated['requestfee_initialvalue'] ?? 0;
        $current = $validated['requestfee'] ?? 0;
        $validated['requestfee_balance'] = $initial - $current;  
        
        // ▼ クライアントから client_type を取得し consultation_party に設定
        $client = Client::find($validated['client_id']);
        $validated['consultation_party'] = $client?->client_type ?? null;

        Business::create([
            'client_id' => $validated['client_id'],
            'consultation_id' => $validated['consultation_id'],
            'consultation_party' => $validated['consultation_party'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'case_summary' => $validated['case_summary'],
            'special_notes' => $validated['special_notes'],
            'inquirytype' => $validated['inquirytype'],
            'consultationtype' => $validated['consultationtype'],
            'case_category' => $validated['case_category'],
            'case_subcategory' => $validated['case_subcategory'],
            'appointment_date' => $validated['appointment_date'],
            'status_limitday' => $validated['status_limitday'],
            'office_id' => $validated['office_id'],
            'lawyer_id' => $validated['lawyer_id'],
            'paralegal_id' => $validated['paralegal_id'],
            'feefinish_prospect' => $validated['feefinish_prospect'],
            'feesystem' => $validated['feesystem'],
            'sales_prospect' => $validated['sales_prospect'],
            'feesystem_initialvalue' => $validated['feesystem_initialvalue'],
            'sales_reason_updated' => $validated['sales_reason_updated'],
            'enddate_prospect' => $validated['enddate_prospect'],
            'enddate_prospect_initialvalue' => $validated['enddate_prospect_initialvalue'],
            'deposit' => $validated['deposit'],
            'performance_reward' => $validated['performance_reward'],
            'difference' => $validated['difference'],
            'requestfee_initialvalue' => $validated['requestfee_initialvalue'],
            'requestfee' => $validated['requestfee'],
            'requestfee_balance' => $validated['requestfee_balance'],
            'route' => $validated['route'],
            'routedetail' => $validated['routedetail'],
            'introducer' => $validated['introducer'],
            'introducer_others' => $validated['introducer_others'],
            'comment' => $validated['comment'],
            'progress_comment' => $validated['progress_comment'],
        ]);

        return redirect()->route('business.index')->with('success', '受任案件を追加しました！');
    }

    // 受任案件詳細処理
    public function show(Business $business)
    {
        $business->load([
            'client',
            'consultation',
            'lawyer',
            'lawyer2',
            'lawyer3',
            'paralegal',
            'paralegal2',
            'paralegal3',
            'courtTasks',
            'relatedParties',
        ]);

        return view('business.show', [
            'business' => $business,
            'relatedparties' => $business->relatedParties,
            'courtTasks' => $business->courtTasks,
        ]);
    }

    // 受任案件編集画面
        public function update(Request $request, Business $business)
    {

        $validator = Validator::make($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'advisory_id' => 'nullable|integer',
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.business_statuses'))),
            'status_detail' => 'nullable|string|max:255',
            'case_summary' => 'required|string|max:1000',
            'special_notes' => 'nullable|string|max:1000',
            'inquirytype' => 'required|in:' . implode(',', array_keys(config('master.inquirytypes'))),
            'consultationtype' => 'required|in:' . implode(',', array_keys(config('master.consultation_types'))),
            'case_category' => 'required|in:' . implode(',', array_keys(config('master.case_categories'))),
            'case_subcategory' => 'required|in:' . implode(',', array_keys(config('master.case_subcategories'))),
            'appointment_date' => 'required|date',
            'close_date' => 'nullable|date',
            'close_notreason' => 'nullable|in:' . implode(',', array_keys(config('master.close_notreasons'))),
            'status_limitday' => 'nullable|date',
            'office_id' => 'required|in:' . implode(',', array_keys(config('master.offices_id'))),
            'lawyer_id' => 'required|exists:users,id',
            'lawyer2_id' => 'nullable|exists:users,id',
            'lawyer3_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'required|exists:users,id',
            'paralegal2_id' => 'nullable|exists:users,id',
            'paralegal3_id' => 'nullable|exists:users,id',
            'feefinish_prospect' => 'required|string|max:255',
            'feesystem' => 'required|string|max:255',
            'sales_prospect' => 'required|numeric',
            'feesystem_initialvalue' => 'required|numeric',
            'sales_reason_updated' => 'required|date',
            'enddate_prospect' => 'required|date',
            'enddate_prospect_initialvalue' => 'required|date',
            'delay_check' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'deposit' => 'required|numeric',
            'performance_reward' => 'required|numeric',
            'difference' => 'required|numeric',
            'requestfee_initialvalue' => 'required|numeric',
            'requestfee' => 'required|numeric',
            'requestfee_balance' => 'required|numeric',
            'childsupport_collect' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'childsupport_phase' => 'nullable|in:' . implode(',', array_keys(config('master.childsupport_phases'))),
            'childsupport_monthly_fee' => 'nullable|numeric',
            'childsupport_monthly_remuneration' => 'nullable|numeric',
            'childsupport_notcollected_amount' => 'nullable|numeric',
            'childsupport_remittance_amount' => 'nullable|numeric',
            'childsupport_payment_date' => 'nullable|date',
            'childsupport_start_payment' => 'nullable|date',
            'childsupport_end_payment' => 'nullable|date',
            'childsupport_deposit_account' => 'nullable|string|max:255',
            'childsupport_deposit_date' => 'nullable|date',
            'childsupport_transfersource_name' => 'nullable|string|max:255',
            'childsupport_repayment_date' => 'nullable|date',
            'childsupport_financialinstitution_name' => 'nullable|string|max:255',
            'childsupport_refundaccount_name' => 'nullable|string|max:255',            
            'childsupport_temporary_payment' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'childsupport_memo' => 'nullable|string|max:1000',
            'route' => 'required|in:' . implode(',', array_keys(config('master.routes'))),
            'routedetail' => 'required|in:' . implode(',', array_keys(config('master.routedetails'))),
            'introducer' => 'nullable|string|max:255',
            'introducer_others' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:255',
            'progress_comment' => 'nullable|string|max:255',
        ]);


        // ✳ ステータスに応じた追加チェック
        $validator->after(function ($validator) use ($request) {
            if ((int)$request->status === 4) {
                if (empty($request->close_date)) {
                    $validator->errors()->add('close_date', 'クローズ時は「終結日」を入力してください。');
                }
                if (empty($request->close_notreason)) {
                    $validator->errors()->add('close_notreason', 'クローズ時は「クローズ理由」を入力してください。');
                }            
                // 差額チェック（0であること）
                if ((float)$request->difference !== 0.0) {
                    $validator->errors()->add('difference', 'クローズ時は「差額」が0である必要があります。');
                }
                // 預り金残チェック（0であること）
                if ((float)$request->requestfee_balance !== 0.0) {
                    $validator->errors()->add('requestfee_balance', 'クローズ時は「預り金残」が0である必要があります。');
                }
            }
        });

        $validated = $validator->validate();

        // 差額の再計算
        $sales_prospect = $validated['sales_prospect'] ?? 0;
        $deposit = $validated['deposit'] ?? 0;
        $reward = $validated['performance_reward'] ?? 0;
        $validated['difference'] = $sales_prospect - $deposit - $reward;

        // 預り金残の再計算
        $initial = $validated['requestfee_initialvalue'] ?? 0;
        $current = $validated['requestfee'] ?? 0;
        $validated['requestfee_balance'] = $initial - $current;

        $business->update([
            'client_id' => $validated['client_id'],
            'consultation_id' => $validated['consultation_id'],
            'advisory_id' => $validated['advisory_id'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'status_detail' => $validated['status_detail'],
            'case_summary' => $validated['case_summary'],
            'special_notes' => $validated['special_notes'],
            'inquirytype' => $validated['inquirytype'],
            'consultationtype' => $validated['consultationtype'],
            'case_category' => $validated['case_category'],
            'case_subcategory' => $validated['case_subcategory'],
            'appointment_date' => $validated['appointment_date'],
            'close_date' => $validated['close_date'],
            'close_notreason' => $validated['close_notreason'],
            'status_limitday' => $validated['status_limitday'],
            'office_id' => $validated['office_id'],
            'lawyer_id' => $validated['lawyer_id'],
            'lawyer2_id' => $validated['lawyer2_id'],
            'lawyer3_id' => $validated['lawyer3_id'],
            'paralegal_id' => $validated['paralegal_id'],
            'paralegal2_id' => $validated['paralegal2_id'],
            'paralegal3_id' => $validated['paralegal3_id'],
            'feefinish_prospect' => $validated['feefinish_prospect'],
            'feesystem' => $validated['feesystem'],
            'sales_prospect' => $validated['sales_prospect'],
            'feesystem_initialvalue' => $validated['feesystem_initialvalue'],
            'sales_reason_updated' => $validated['sales_reason_updated'],
            'enddate_prospect' => $validated['enddate_prospect'],
            'enddate_prospect_initialvalue' => $validated['enddate_prospect_initialvalue'],
            'delay_check' => $validated['delay_check'],
            'deposit' => $validated['deposit'],
            'performance_reward' => $validated['performance_reward'],
            'difference' => $validated['difference'],
            'requestfee_initialvalue' => $validated['requestfee_initialvalue'],
            'requestfee' => $validated['requestfee'],
            'requestfee_balance' => $validated['requestfee_balance'],
            'childsupport_collect' => $validated['childsupport_collect'],
            'childsupport_phase' => $validated['childsupport_phase'],
            'childsupport_monthly_fee' => $validated['childsupport_monthly_fee'],
            'childsupport_monthly_remuneration' => $validated['childsupport_monthly_remuneration'],
            'childsupport_notcollected_amount' => $validated['childsupport_notcollected_amount'],
            'childsupport_remittance_amount' => $validated['childsupport_remittance_amount'],
            'childsupport_payment_date' => $validated['childsupport_payment_date'],
            'childsupport_start_payment' => $validated['childsupport_start_payment'],
            'childsupport_end_payment' => $validated['childsupport_end_payment'],
            'childsupport_deposit_account' => $validated['childsupport_deposit_account'],
            'childsupport_deposit_date' => $validated['childsupport_deposit_date'],
            'childsupport_transfersource_name' => $validated['childsupport_transfersource_name'],
            'childsupport_repayment_date' => $validated['childsupport_repayment_date'],
            'childsupport_financialinstitution_name' => $validated['childsupport_financialinstitution_name'],
            'childsupport_refundaccount_name' => $validated['childsupport_refundaccount_name'],
            'childsupport_temporary_payment' => $validated['childsupport_temporary_payment'],
            'childsupport_memo' => $validated['childsupport_memo'],
            'route' => $validated['route'],
            'routedetail' => $validated['routedetail'],
            'introducer' => $validated['introducer'],
            'introducer_others' => $validated['introducer_others'],
            'comment' => $validated['comment'],
            'progress_comment' => $validated['progress_comment'],
        ]);
        return redirect()->route('business.show', $business->id)->with('success', '受任案件を更新しました！');
    }

    // 受任案件削除処理
    public function destroy(Business $business)
    {
        $this->ensureIsAdmin();
        $business->delete();
        return redirect()->route('business.index')->with('success', '受任案件を削除しました！');
    }

    // 受任案件検索API
    public function search(Request $request)
    {
        $keyword = $request->input('q');
    
        $results = [];
    
        if ($keyword) {
            $results = Business::where('title', 'like', "%{$keyword}%")
                ->select('id', 'title')
                ->limit(10)
                ->get()
                ->map(fn($business) => ['id' => $business->id, 'text' => $business->title]);
        }
    
        return response()->json(['results' => $results]);
    }
    

}
