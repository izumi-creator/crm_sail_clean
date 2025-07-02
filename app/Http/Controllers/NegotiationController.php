<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Negotiation;
use App\Models\Consultation;
use App\Models\Business;
use App\Models\AdvisoryContract;
use App\Models\AdvisoryConsultation;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class NegotiationController extends Controller
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

    // 折衝履歴一覧画面
    public function index(Request $request)
    {
        $query = Negotiation::query();

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }
        // worker名で検索
        if ($request->filled('worker_name')) {
            $workerIds = User::where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->worker_name . '%');
            })->pluck('id');

            $query->whereIn('worker_id', $workerIds);
        }
        if ($request->filled('deadline_date')) {
            $query->where('deadline_date', $request->deadline_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $negotiations = $query->with('worker')->paginate(15);
        return view('negotiation.index', compact('negotiations'));
    }

    // 折衝履歴登録画面
    public function create()
    {
        return view('negotiation.create');
    }
    // 折衝履歴登録処理
    public function store(Request $request)
    {

        // ▼ Select2の初期テキスト表示対応（相談）
        if ($request->has('consultation_id')) {
            $consultation = Consultation::find($request->input('consultation_id'));
            if ($consultation) {
                $request->merge([
                    'consultation_name_display' => $consultation->title,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（受任案件）
        if ($request->has('business_id')) {
            $business = Business::find($request->input('business_id'));
            if ($business) {
                $request->merge([
                    'business_name_display' => $business->title,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（顧問契約）
        if ($request->has('advisory_contract_id')) {
            $advisoryContract = AdvisoryContract::find($request->input('advisory_contract_id'));
            if ($advisoryContract) {
                $request->merge([
                    'advisory_contract_name_display' => $advisoryContract->title,
                ]);
            }
        }

        // ▼ Select2の初期テキスト表示対応（顧問相談）
        if ($request->has('advisory_consultation_id')) {
            $advisoryConsultation = AdvisoryConsultation::find($request->input('advisory_consultation_id'));
            if ($advisoryConsultation) {
                $request->merge([
                    'advisory_consultation_name_display' => $advisoryConsultation->title,
                ]);
            }
        }

        $validator = Validator::make($request->all(), [
            'related_party' => 'required|in:' . implode(',', array_keys(config('master.related_parties'))),
            'consultation_id' => 'nullable|exists:consultations,id',
            'business_id' => 'nullable|exists:businesses,id',
            'advisory_contract_id' => 'nullable|exists:advisory_contracts,id',
            'advisory_consultation_id' => 'nullable|exists:advisory_consultations,id',
            'record1' => 'required|in:' . implode(',', array_keys(config('master.records_1'))),
            'record2' => 'required|in:' . implode(',', array_keys(config('master.records_2'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.task_statuses'))),
            'already_read' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'record_date' => 'nullable|date',
            'content' => 'nullable|string|max:1000',
            'orderer_id' => 'nullable|exists:users,id',
            'worker_id' => 'required|exists:users,id',
            'attachment1_title' => 'nullable|string|max:255',
            'attachment2_title' => 'nullable|string|max:255',
            'attachment3_title' => 'nullable|string|max:255',
            'link1' => 'nullable|url',
            'link2' => 'nullable|url',
            'link3' => 'nullable|url',
            'phone_request' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'notify_type' => 'nullable|in:' . implode(',', array_keys(config('master.notify_types'))),
            'record_to' => 'nullable|string|max:255',
            'phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_to' => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_from' => 'nullable|regex:/^[0-9]+$/|max:15',
            'naisen_to' => 'nullable|regex:/^[0-9]+$/|max:15',
            'naisen_from' => 'nullable|regex:/^[0-9]+$/|max:15',
            'notify_person_in' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
        ]);

        $validator->after(function ($validator) use ($request) {
            switch ((int)$request->related_party) {
                case 1:
                    if (empty($request->consultation_id)) {
                        $validator->errors()->add('consultation_id', '「相談」を入力してください。');
                    }
                    break;
                case 2:
                    if (empty($request->business_id)) {
                        $validator->errors()->add('business_id', '「受任案件」を入力してください。');
                    }
                    break;
                case 3:
                    if (empty($request->advisory_contract_id)) {
                        $validator->errors()->add('advisory_contract_id', '「顧問契約」を入力してください。');
                    }
                    break;
                case 4:
                    if (empty($request->advisory_consultation_id)) {
                        $validator->errors()->add('advisory_consultation_id', '「顧問相談」を入力してください。');
                    }
                    break;
            }
        });

        $validated = $validator->validate();

        Negotiation::create([
            'related_party' => $validated['related_party'],
            'consultation_id' => $validated['consultation_id'],
            'business_id' => $validated['business_id'],
            'advisory_contract_id' => $validated['advisory_contract_id'],
            'advisory_consultation_id' => $validated['advisory_consultation_id'],
            'record1' => $validated['record1'],
            'record2' => $validated['record2'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'already_read' => $validated['already_read'] ?? '0',
            'record_date' => $validated['record_date'],
            'content' => $validated['content'],
            'orderer_id' => $validated['orderer_id'],
            'worker_id' => $validated['worker_id'],
            'attachment1_title' => $validated['attachment1_title'],
            'attachment2_title' => $validated['attachment2_title'],
            'attachment3_title' => $validated['attachment3_title'],
            'link1' => $validated['link1'],
            'link2' => $validated['link2'],
            'link3' => $validated['link3'],
            'phone_request' => $validated['phone_request'] ?? '0',
            'notify_type' => $validated['notify_type'],
            'record_to' => $validated['record_to'],
            'phone_number' => $validated['phone_number'],
            'phone_to' => $validated['phone_to'],
            'phone_from' => $validated['phone_from'],
            'naisen_to' => $validated['naisen_to'],
            'naisen_from' => $validated['naisen_from'],
            'notify_person_in' => $validated['notify_person_in'] ?? '0',
        ]);

        if ($request->filled('redirect_url')) {
        return redirect($request->input('redirect_url'))->with('success', '折衝履歴を登録しました！');
        }

        return redirect()->route('negotiation.index')->with('success', '折衝履歴が登録しました！');
    }

    // 折衝履歴詳細処理
    public function show(Negotiation $negotiation)
    {
        // 関連データをロード
        $negotiation->load([
            'consultation',
            'business',
            'advisoryContract',
            'advisoryConsultation',
            'orderer',
            'worker',
        ]);

        return view('negotiation.show', compact('negotiation'));
    }

    // 折衝履歴編集画面
    public function update(Request $request, Negotiation $negotiation)
    {
        $validator = Validator::make($request->all(), [
            'record1' => 'required|in:' . implode(',', array_keys(config('master.records_1'))),
            'record2' => 'required|in:' . implode(',', array_keys(config('master.records_2'))),
            'title' => 'required|string|max:255',
            'status' => 'required|in:' . implode(',', array_keys(config('master.task_statuses'))),
            'already_read' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'record_date' => 'nullable|date',
            'content' => 'nullable|string|max:1000',
            'orderer_id' => 'nullable|exists:users,id',
            'worker_id' => 'required|exists:users,id',
            'attachment1_title' => 'nullable|string|max:255',
            'attachment2_title' => 'nullable|string|max:255',
            'attachment3_title' => 'nullable|string|max:255',
            'link1' => 'nullable|url',
            'link2' => 'nullable|url',
            'link3' => 'nullable|url',
            'phone_request' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
            'notify_type' => 'nullable|in:' . implode(',', array_keys(config('master.notify_types'))),
            'record_to' => 'nullable|string|max:255',
            'phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_to' => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_from' => 'nullable|regex:/^[0-9]+$/|max:15',
            'naisen_to' => 'nullable|regex:/^[0-9]+$/|max:15',
            'naisen_from' => 'nullable|regex:/^[0-9]+$/|max:15',
            'notify_person_in' => 'nullable|in:' . implode(',', array_keys(config('master.checks'))),
        ]);

        $validated = $validator->validate();

        $negotiation->update([
            'record1' => $validated['record1'],
            'record2' => $validated['record2'],
            'title' => $validated['title'],
            'status' => $validated['status'],
            'already_read' => $validated['already_read'] ?? '0',
            'record_date' => $validated['record_date'],
            'content' => $validated['content'],
            'orderer_id' => $validated['orderer_id'],
            'worker_id' => $validated['worker_id'],
            'attachment1_title' => $validated['attachment1_title'],
            'attachment2_title' => $validated['attachment2_title'],
            'attachment3_title' => $validated['attachment3_title'],
            'link1' => $validated['link1'],
            'link2' => $validated['link2'],
            'link3' => $validated['link3'],
            'phone_request' => $validated['phone_request'] ?? '0',
            'notify_type' => $validated['notify_type'],
            'record_to' => $validated['record_to'],
            'phone_number' => $validated['phone_number'],
            'phone_to' => $validated['phone_to'],
            'phone_from' => $validated['phone_from'],
            'naisen_to' => $validated['naisen_to'],
            'naisen_from' => $validated['naisen_from'],
            'notify_person_in' => $validated['notify_person_in'] ?? '0',
        ]);

        return redirect()->route('negotiation.show', $negotiation)->with('success', '折衝履歴を更新しました！');
    }
    // 折衝履歴削除画面
    public function destroy(Negotiation $negotiation)
    {
        $this->ensureIsAdmin();
        try {
            $negotiation->delete();
            return redirect()->route('negotiation.index')->with('success', '折衝履歴を削除しました');
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
    
}
