<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Client;
use App\Models\User;
use App\Models\RelatedParty;
use App\Models\Business;
use App\Models\AdvisoryConsultation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ConsultationController extends Controller
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

    // 相談一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = Consultation::query();

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

        $consultations = $query->with('client')->paginate(15);
        return view('consultation.index', compact('consultations'));
    }

    // 相談登録画面
    public function create()
    {
        return view('consultation.create');
    }

    // 相談登録処理
    public function store(Request $request)
    {
        $clientMode = $request->input('client_mode'); // 'existing' or 'new'

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


        $clientId = null;
        $consultationParty = null;

        // ▼ クライアント関連のバリデーションと登録
        if ($clientMode === 'new') {
            $request->validate([
                'client_type' => 'required|in:individual,corporation',
            ]);

            $clientTypeRaw = $request->input('client_type'); // 'individual' or 'corporation'

            if ($clientTypeRaw === 'individual') {
                $request->validate([
                    'individual.last_name_kanji' => 'required|string|max:50',
                    'individual.first_name_kanji' => 'required|string|max:50',
                    'individual.last_name_kana' => 'required|string|max:50',
                    'individual.first_name_kana' => 'required|string|max:50',
                    'individual.name_kanji' => 'required|string|max:100',
                    'individual.name_kana' => 'required|string|max:100',
                ]);
                $clientData = $request->input('individual');
                $clientTypes = 1;
            } else {
                $request->validate([
                    'corporate.name_kanji' => 'required|string|max:100',
                    'corporate.name_kana' => 'required|string|max:100',
                ]);
                $clientData = $request->input('corporate');
                $clientTypes = 2;
            }

            $client = new Client();
            $client->fill($clientData);
            $client->client_type = $clientTypes;
            $client->save();

            $clientId = $client->id;
            $consultationParty = $clientTypes; // 1 = 個人, 2 = 法人

        } else {
            // 既存クライアント使用
            $request->validate([
                'client_id' => 'required|exists:clients,id',
            ]);
            $clientId = $request->input('client_id');

            // Clientモデルから client_type を取得し、consultation_party に使う
            $existingClient = Client::find($clientId);
            $consultationParty = $existingClient?->client_type ?? 1; // 安全に fallback
        }

        // ▼ 相談関連バリデーション
        $request->validate([
            'title' => 'required|string|max:255',
            'inquirytype' => 'required|in:' . implode(',', array_keys(config('master.inquirytypes'))),
            'office_id' => 'required|in:' . implode(',', array_keys(config('master.offices_id'))),
            'status' => 'required|in:' . implode(',', array_keys(config('master.consultation_statuses'))),
            'lawyer_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'nullable|exists:users,id',
        ]);

        // ▼ 相談データ登録
        $consultation = Consultation::create([ 
            'client_id' => $clientId,
            'consultation_party' => $consultationParty,
            'title' => $request->title,
            'inquirytype' => $request->inquirytype,
            'office_id' => $request->office_id,
            'status' => $request->status,
            'lawyer_id' => $request->lawyer_id,
            'paralegal_id' => $request->paralegal_id,
        ]);

         // ▼ 関係者関連のバリデーションと登録    
        foreach ([0, 1] as $index) {
            if ($request->filled("participants.$index.name_kanji")) {
                $request->validate([
                    "participants.$index.party" => 'required|in:' . implode(',', array_keys(config('master.relatedparties_parties'))),
                    "participants.$index.class" => 'required|in:' . implode(',', array_keys(config('master.relatedparties_classes'))),
                    "participants.$index.type" => 'required|in:' . implode(',', array_keys(config('master.relatedparties_types'))),
                    "participants.$index.position" => 'required|in:' . implode(',', array_keys(config('master.relatedparties_positions'))),
                    "participants.$index.name_kanji" => 'required|string|max:255',
                    "participants.$index.manager_name_kanji" => 'nullable|string|max:255',
                    "participants.$index.manager_name_kana" => 'nullable|string|max:255',
                ]);
            }
        }
        
        foreach ([0, 1] as $index) {
            $p = $request->input("participants.$index");
            if ($p && !empty($p['name_kanji'])) {
                RelatedParty::create([
                    'consultation_id' => $consultation->id,
                    'relatedparties_party' => $p['party'],
                    'relatedparties_class' => $p['class'],
                    'relatedparties_type' => $p['type'],
                    'relatedparties_position' => $p['position'],
                    'relatedparties_name_kanji' => $p['name_kanji'],
                    'manager_name_kanji' => $p['manager_name_kanji'] ?? null,
                    'manager_name_kana' => $p['manager_name_kana'] ?? null,
                ]);
            }
        }

        $participants = $request->input('participants', []);

        $hasParticipants = collect($participants)->filter(function ($p) {
            return !empty($p['name_kanji']);
        })->isNotEmpty();

        $message = '相談を追加しました！';

        if ($clientMode === 'new' && $hasParticipants) {
            $message = '相談・クライアント・関係者を追加しました！';
        } elseif ($clientMode === 'new') {
            $message = '相談・クライアントを追加しました！';
        } elseif ($hasParticipants) {
            $message = '相談・関係者を追加しました！';
        }

        return redirect()->route('consultation.index')->with('success', $message);

    }

    // 相談詳細処理
    public function show(Consultation $consultation)
    {

        $consultation->load([
            'client',
            'lawyer',
            'lawyer2',
            'lawyer3',
            'paralegal',
            'paralegal2',
            'paralegal3',
            'business',
            'relatedParties',
            'advisoryConsultation',
        ]);

        return view('consultation.show',  compact('consultation'));

    }
    
    // 相談編集処理
    public function update(Request $request, Consultation $consultation)
    {

        $before_status = $consultation->status;

        $validator = Validator::make($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'business_id' => 'nullable|exists:businesses,id',
            'advisory_consultation_id' => 'nullable|exists:advisory_consultations,id',
            'consultation_party' => 'required|in:' . implode(',', array_keys(config('master.consultation_parties'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.consultation_statuses'))),
            'status_detail' => 'nullable|string|max:255',
            'case_summary' => 'required|string|max:1000',
            'special_notes' => 'nullable|string|max:1000',
            'inquirycontent' => 'required|string|max:1000',
            'firstchoice_date' => 'nullable|date',
            'firstchoice_time' => 'nullable|date_format:H:i',
            'secondchoice_date' => 'nullable|date',
            'secondchoice_time' => 'nullable|date_format:H:i',
            'inquirytype' => 'required|in:' . implode(',', array_keys(config('master.inquirytypes'))),
            'consultationtype' => 'required|in:' . implode(',', array_keys(config('master.consultation_types'))),
            'case_category' => 'required|in:' . implode(',', array_keys(config('master.case_categories'))),
            'case_subcategory' => 'required|in:' . implode(',', array_keys(config('master.case_subcategories'))),
            'opponent_confliction' => 'nullable|in:' . implode(',', array_keys(config('master.opponent_conflictions'))),
            'consultation_receptiondate' => 'nullable|date',
            'consultation_firstdate' => 'nullable|date',
            'enddate' => 'nullable|date',
            'consultation_notreason' => 'nullable|in:' . implode(',', array_keys(config('master.consultation_notreasons'))),
            'consultation_feedback' => 'nullable|in:' . implode(',', array_keys(config('master.consultation_feedbacks'))),
            'reason_termination' => 'nullable|string|max:255',
            'reason_termination_detail' => 'nullable|string|max:255',
            'office_id' => 'required|in:' . implode(',', array_keys(config('master.offices_id'))),
            'lawyer_id' => 'required|exists:users,id',
            'lawyer2_id' => 'nullable|exists:users,id',
            'lawyer3_id' => 'nullable|exists:users,id',
            'paralegal_id' => 'required|exists:users,id',
            'paralegal2_id' => 'nullable|exists:users,id',
            'paralegal3_id' => 'nullable|exists:users,id',
            'feefinish_prospect' => 'nullable|string|max:255',
            'feesystem' => 'nullable|string|max:255',
            'sales_prospect' => 'nullable|numeric',
            'feesystem_initialvalue' => 'nullable|numeric',
            'sales_reason_updated' => 'nullable|date',
            'enddate_prospect' => 'nullable|date',
            'enddate_prospect_initialvalue' => 'nullable|date',
            'route' => 'nullable|in:' . implode(',', array_keys(config('master.routes'))),
            'routedetail' => 'nullable|in:' . implode(',', array_keys(config('master.routedetails'))),
            'introducer' => 'nullable|string|max:255',
            'introducer_others' => 'nullable|string|max:255',
        ]);

        // ✳ ステータスに応じた追加チェック
        $validator->after(function ($validator) use ($request) {

            if (in_array((int)$request->status, [3, 5, 6])) {
                if ((int)$request->opponent_confliction !== 1) {
                    $validator->errors()->add('opponent_confliction', '「利益相反確認」がチェックされておりません。');
                }
                if (empty($request->consultation_receptiondate)) {
                    $validator->errors()->add('consultation_receptiondate', '「相談受付日」を入力してください。');
                }
                if (empty($request->consultation_firstdate)) {
                    $validator->errors()->add('consultation_firstdate', '「相談初回日」を入力してください。');
                }
                if (empty($request->feefinish_prospect)) {
                    $validator->errors()->add('feefinish_prospect', '「見込理由」を入力してください。');
                }
                if (empty($request->feesystem)) {
                    $validator->errors()->add('feesystem', '「報酬体系」を入力してください。');
                }
                if (empty($request->sales_prospect)) {
                    $validator->errors()->add('sales_prospect', '「売上見込」を入力してください。');
                }
                if (empty($request->feesystem_initialvalue)) {
                    $validator->errors()->add('feesystem_initialvalue', '「売上見込（初期値）」を入力してください。');
                }
                if (empty($request->sales_reason_updated)) {
                    $validator->errors()->add('sales_reason_updated', '「売上見込更新日」を入力してください。');
                }
                if (empty($request->enddate_prospect)) {
                    $validator->errors()->add('enddate_prospect', '「終了時期見込」を入力してください。');
                }
                if (empty($request->enddate_prospect_initialvalue)) {
                    $validator->errors()->add('enddate_prospect_initialvalue', '「終了時期見込（初期値）」を入力してください。');
                }
            }

            if ((int)$request->status === 4) {
                if (empty($request->enddate)) {
                    $validator->errors()->add('enddate', '「終了日」を入力してください。');
                }
                if (empty($request->consultation_notreason)) {
                    $validator->errors()->add('consultation_notreason', '「相談に至らなかった理由」を入力してください。');
                }
                if (empty($request->consultation_feedback)) {
                    $validator->errors()->add('consultation_feedback', '「相談後のフィードバック」を入力してください。');
                }
                if (empty($request->reason_termination)) {
                    $validator->errors()->add('reason_termination', '「相談終了理由」を入力してください。');
                }
                if (empty($request->reason_termination_detail)) {
                    $validator->errors()->add('reason_termination_detail', '「相談終了理由（詳細）」を入力してください。');
                }
                if (empty($request->route)) {
                    $validator->errors()->add('route', '「流入経路」を入力してください。');
                }
                if (empty($request->routedetail)) {
                    $validator->errors()->add('routedetail', '「流入経路（詳細）」を入力してください。');
                }
            }

            if ((int)$request->status === 6) {
                if (empty($request->enddate)) {
                    $validator->errors()->add('enddate', '「終了日」を入力してください。');
                }
                if (empty($request->consultation_feedback)) {
                    $validator->errors()->add('consultation_feedback', '「相談後のフィードバック」を入力してください。');
                }
                if (empty($request->reason_termination)) {
                    $validator->errors()->add('reason_termination', '「相談終了理由」を入力してください。');
                }
                if (empty($request->reason_termination_detail)) {
                    $validator->errors()->add('reason_termination_detail', '「相談終了理由（詳細）」を入力してください。');
                }
                if (empty($request->route)) {
                    $validator->errors()->add('route', '「流入経路」を入力してください。');
                }
                if (empty($request->routedetail)) {
                    $validator->errors()->add('routedetail', '「流入経路（詳細）」を入力してください。');
                }
            }
        });

        $selectedClient = Client::find($request->client_id);
        if ($selectedClient && $selectedClient->client_type !== (int) $request->consultation_party) {
            return back()
                ->withErrors(['client_id' => 'クライアントの種別（個人/法人）と、相談の区分が一致していません。'])
                ->withInput();
        }

        $firstChoice = null;
        if ($request->filled('firstchoice_date') && $request->filled('firstchoice_time')) {
            $firstChoice = \Carbon\Carbon::parse($request->firstchoice_date . ' ' . $request->firstchoice_time);
        }

        $secondChoice = null;
        if ($request->filled('secondchoice_date') && $request->filled('secondchoice_time')) {
            $secondChoice = \Carbon\Carbon::parse($request->secondchoice_date . ' ' . $request->secondchoice_time);
        }

        $validated = $validator->validate();

        $consultation->update([
            'client_id' => $validated['client_id'],
            'business_id' => $validated['business_id'],
            'advisory_consultation_id' => $validated['advisory_consultation_id'],
            'consultation_party' => $validated['consultation_party'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'status_detail' => $validated['status_detail'],
            'case_summary' => $validated['case_summary'],
            'special_notes' => $validated['special_notes'],
            'inquirycontent' => $validated['inquirycontent'],
            'firstchoice_datetime' => $firstChoice,
            'secondchoice_datetime' => $secondChoice,
            'inquirytype' => $validated['inquirytype'],
            'consultationtype' => $validated['consultationtype'],
            'case_category' => $validated['case_category'],
            'case_subcategory' => $validated['case_subcategory'],
            'opponent_confliction' => $validated['opponent_confliction'],
            'consultation_receptiondate' => $validated['consultation_receptiondate'],
            'consultation_firstdate' => $validated['consultation_firstdate'],
            'enddate' => $validated['enddate'],
            'consultation_notreason' => $validated['consultation_notreason'],
            'consultation_feedback' => $validated['consultation_feedback'],
            'reason_termination' => $validated['reason_termination'],
            'reason_termination_detail' => $validated['reason_termination_detail'],
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
            'route' => $validated['route'],
            'routedetail' => $validated['routedetail'],
            'introducer' => $validated['introducer'],
            'introducer_others' => $validated['introducer_others'],
        ]);

        $messages = ['相談が更新されました。'];

        $before_status = (int) $before_status;
        $after_status = (int) $validated['status'];

        if ($before_status !== 6 && $after_status === 6) {
            $business = $this->generateBusinessFromConsultation($consultation);
        
            if ($business->wasRecentlyCreated) {
                $messages[] = "▶ 受任案件が新規作成されました（案件ID: #{$business->id}）。";
            
                $count = RelatedParty::where('consultation_id', $consultation->id)->count();
                if ($count > 0) {
                    $messages[] = "▶ 関係者{$count}名に受任案件を自動設定しました。";
                }
            } else {
                $messages[] = "▶ 受任案件はすでに作成されています（案件ID: #{$business->id}）。";
            }
        }

        return redirect()
            ->route('consultation.show', $consultation->id)
            ->with('success', implode("\n", $messages));

    }

    private function generateBusinessFromConsultation(Consultation $consultation)
    {

    $business = Business::firstOrCreate(
        ['consultation_id' => $consultation->id],
        [
            'client_id' => $consultation->client_id,
            'advisory_consultation_id' => $consultation->advisory_consultation_id,
            'consultation_party' => $consultation->consultation_party,
            'status' => 1, // 初期ステータス
            'title' => $consultation->title,
            'case_summary' => $consultation->case_summary,
            'special_notes' => $consultation->special_notes,
            'inquirytype' => $consultation->inquirytype,
            'consultationtype' => $consultation->consultationtype,
            'case_category' => $consultation->case_category,
            'case_subcategory' => $consultation->case_subcategory,
            'office_id' => $consultation->office_id,
            'lawyer_id' => $consultation->lawyer_id,
            'paralegal_id' => $consultation->paralegal_id,
            'lawyer2_id' => $consultation->lawyer2_id,
            'paralegal2_id' => $consultation->paralegal2_id,
            'lawyer3_id' => $consultation->lawyer3_id,
            'paralegal3_id' => $consultation->paralegal3_id,
            'feefinish_prospect' => $consultation->feefinish_prospect,
            'feesystem' => $consultation->feesystem,
            'sales_prospect' => $consultation->sales_prospect,
            'feesystem_initialvalue' => $consultation->feesystem_initialvalue,
            'sales_reason_updated' => $consultation->sales_reason_updated,
            'enddate_prospect' => $consultation->enddate_prospect,
            'enddate_prospect_initialvalue' => $consultation->enddate_prospect_initialvalue,
            'route' => $consultation->route,
            'routedetail' => $consultation->routedetail,
            'introducer' => $consultation->introducer,
            'introducer_others' => $consultation->introducer_others,
        ]
        );

        // 関係者に business_id を紐づけ（新規作成時のみ）
        if ($business->wasRecentlyCreated) {
            RelatedParty::where('consultation_id', $consultation->id)
                ->update(['business_id' => $business->id]);
        }

        // 相談に business_id を紐づけ（新規作成時のみ）
        $consultation->business_id = $business->id;
        $consultation->save();

        return $business;
    }


    // 相談削除処理
    public function destroy(Consultation $consultation)
    {
        $this->ensureIsAdmin();
        $consultation->delete();
        return redirect()->route('consultation.index')->with('success', '相談を削除しました！');
    }

    //相談検索API
    public function search(Request $request)
    {
        $keyword = $request->input('q');
    
        $results = [];
    
        if ($keyword) {
            $results = Consultation::where('title', 'like', "%{$keyword}%")
                ->select('id', 'title')
                ->limit(10)
                ->get()
                ->map(fn($consultation) => ['id' => $consultation->id, 'text' => $consultation->title]);
        }
    
        return response()->json(['results' => $results]);
    }
    

}
