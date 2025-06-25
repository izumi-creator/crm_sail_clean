<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RelatedParty;
use App\Models\Consultation;
use App\Models\Client;
use App\Models\Business;
use App\Models\AdvisoryConsultation;
use Illuminate\Validation\Rule;

class RelatedPartyController extends Controller
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

    // 関係者一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = RelatedParty::query();

        if ($request->filled('relatedparties_name_kanji')) {
            $query->where('relatedparties_name_kanji', 'like', '%' . $request->relatedparties_name_kanji . '%');
        }
        if ($request->filled('relatedparties_party')) {
            $query->where('relatedparties_party', $request->relatedparties_party);
        }
        if ($request->filled('relatedparties_class')) {
            $query->where('relatedparties_class', $request->relatedparties_class);
        }
        if ($request->filled('relatedparties_type')) {
            $query->where('relatedparties_type', $request->relatedparties_type);
        }

        $relatedparties = $query->paginate(15);
        return view('relatedparty.index', compact('relatedparties'));
    }

    // 関係者追加画面
    public function create()
    {
        return view('relatedparty.create');
    }
    // 関係者追加処理
    public function store(Request $request)
    {

        if ($request->has('consultation_id')) {
            $consultation = Consultation::find($request->input('consultation_id'));
            if ($consultation) {
                $request->merge([
                    'consultation_name_display' => $consultation->title,
                ]);
            }
        }

        if ($request->has('client_id')) {
            $client = Client::find($request->input('client_id'));
            if ($client) {
                $request->merge([
                    'client_name_display' => $client->name_kanji,
                ]);
            }
        }

        if ($request->has('business_id')) {
            $business = Business::find($request->input('business_id'));
            if ($business) {
                $request->merge([
                    'business_name_display' => $business->title,
                ]);
            }
        }

        if ($request->has('advisory_consultation_id')) {
            $advisoryConsultation = AdvisoryConsultation::find($request->input('advisory_consultation_id'));
            if ($advisoryConsultation) {
                $request->merge([
                    'advisory_name_display' => $advisoryConsultation->title,
                ]);
            }
        }

        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'business_id' => 'nullable|exists:businesses,id',
            'advisory_consultation_id' => 'nullable|exists:advisory_consultations,id',
            'relatedparties_party' => 'required|in:' . implode(',', array_keys(config('master.relatedparties_parties'))),
            'relatedparties_class' => 'required|in:' . implode(',', array_keys(config('master.relatedparties_classes'))),
            'relatedparties_type' => 'required|in:' . implode(',', array_keys(config('master.relatedparties_types'))),
            'relatedparties_position' => 'nullable|in:' . implode(',', array_keys(config('master.relatedparties_positions'))),
            'relatedparties_position_details' => 'nullable|string|max:255',
            'relatedparties_explanation' => 'nullable|string|max:1000',
            'relatedparties_name_kanji' => 'required|string|max:255',
            'relatedparties_name_kana' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'phone_number' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'phone_number2' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'fax' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:100',
            'email2' => 'nullable|email|max:100',
            'relatedparties_postcode' => 'nullable|string|max:10',
            'relatedparties_address' => 'nullable|string|max:255',
            'relatedparties_address2' => 'nullable|string|max:255',
            'placeofwork' => 'nullable|string|max:255',
            'manager_name_kanji' => 'nullable|string|max:255',
            'manager_name_kana' => 'nullable|string|max:255',
            'manager_post' => 'nullable|string|max:100',
            'manager_department' => 'nullable|string|max:100',
        ]);

        RelatedParty::create([
            'client_id' => $request->client_id,
            'consultation_id' => $request->consultation_id,
            'business_id' => $request->business_id,
            'advisory_consultation_id' => $request->advisory_consultation_id,
            'relatedparties_party' => $request->relatedparties_party,
            'relatedparties_class' => $request->relatedparties_class,
            'relatedparties_type' => $request->relatedparties_type,
            'relatedparties_position' => $request->relatedparties_position,
            'relatedparties_position_details' => $request->relatedparties_position_details,
            'relatedparties_explanation' => $request->relatedparties_explanation,
            'relatedparties_name_kanji' => $request->relatedparties_name_kanji,
            'relatedparties_name_kana' => $request->relatedparties_name_kana,
            'mobile_number' => $request->mobile_number,
            'phone_number' => $request->phone_number,
            'phone_number2' => $request->phone_number2,
            'fax' => $request->fax,
            'email' => $request->email,
            'email2' => $request->email2,
            'relatedparties_postcode' => $request->relatedparties_postcode,
            'relatedparties_address' => $request->relatedparties_address,
            'relatedparties_address2' => $request->relatedparties_address2,
            'placeofwork' => $request->placeofwork,
            'manager_name_kanji' => $request->manager_name_kanji,
            'manager_name_kana' => $request->manager_name_kana,
            'manager_post' => $request->manager_post,
            'manager_department' => $request->manager_department,
        ]);
        // 関係者の関連情報があればここで保存処理を追加
        return redirect()->route('relatedparty.index')->with('success', '関係者を追加しました！');
    }

    // 関係者詳細表示
    public function show(RelatedParty $relatedparty)
    {
        $relatedparty->load(['consultation', 'client', 'business', 'advisoryConsultation']);
    
        return view('relatedparty.show', compact('relatedparty'));
    }

    // 関係者更新画面
    public function update(Request $request, RelatedParty $relatedparty)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'business_id' => 'nullable|exists:businesses,id',
            'advisory_consultation_id' => 'nullable|exists:advisory_consultations,id',
            'relatedparties_party' => 'required|in:' . implode(',', array_keys(config('master.relatedparties_parties'))),
            'relatedparties_class' => 'required|in:' . implode(',', array_keys(config('master.relatedparties_classes'))),
            'relatedparties_type' => 'required|in:' . implode(',', array_keys(config('master.relatedparties_types'))),
            'relatedparties_position' => 'nullable|in:' . implode(',', array_keys(config('master.relatedparties_positions'))),
            'relatedparties_position_details' => 'nullable|string|max:255',
            'relatedparties_explanation' => 'nullable|string|max:1000',
            'relatedparties_name_kanji' => 'required|string|max:255',
            'relatedparties_name_kana' => 'nullable|string|max:255',
            'mobile_number' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'phone_number' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'phone_number2' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'fax' => 'nullable|string|max:15|regex:/^[0-9]+$/',
            'email' => 'nullable|email|max:100',
            'email2' => 'nullable|email|max:100',
            'relatedparties_postcode' => 'nullable|string|max:10',
            'relatedparties_address' => 'nullable|string|max:255',
            'relatedparties_address2' => 'nullable|string|max:255',
            'placeofwork' => 'nullable|string|max:255',
            'manager_name_kanji' => 'nullable|string|max:255',
            'manager_name_kana' => 'nullable|string|max:255',
            'manager_post' => 'nullable|string|max:100',
            'manager_department' => 'nullable|string|max:100',
        ]);
        $relatedparty->update([
            'client_id' => $request->client_id,
            'consultation_id' => $request->consultation_id,
            'business_id' => $request->business_id,
            'advisory_consultation_id' => $request->advisory_consultation_id,
            'relatedparties_party' => $request->relatedparties_party,
            'relatedparties_class' => $request->relatedparties_class,
            'relatedparties_type' => $request->relatedparties_type,
            'relatedparties_position' => $request->relatedparties_position,
            'relatedparties_position_details' => $request->relatedparties_position_details,
            'relatedparties_explanation' => $request->relatedparties_explanation,
            'relatedparties_name_kanji' => $request->relatedparties_name_kanji,
            'relatedparties_name_kana' => $request->relatedparties_name_kana,
            'mobile_number' => $request->mobile_number,
            'phone_number' => $request->phone_number,
            'phone_number2' => $request->phone_number2,
            'fax' => $request->fax,
            'email' => $request->email,
            'email2' => $request->email2,
            'relatedparties_postcode' => $request->relatedparties_postcode,
            'relatedparties_address' => $request->relatedparties_address,
            'relatedparties_address2' => $request->relatedparties_address2,
            'placeofwork' => $request->placeofwork,
            'manager_name_kanji' => $request->manager_name_kanji,
            'manager_name_kana' => $request->manager_name_kana,
            'manager_post' => $request->manager_post,
            'manager_department' => $request->manager_department,
        ]);

        return redirect()->route('relatedparty.show', $relatedparty->id)->with('success', '関係者を更新しました！');
    }

    // 関係者削除処理
    public function destroy(RelatedParty $relatedparty)
    {
        $this->ensureIsAdmin(); // 管理者権限チェック
        $relatedparty->delete();
        return redirect()->route('relatedparty.index')->with('success', '関係者を削除しました！');
    }
}
