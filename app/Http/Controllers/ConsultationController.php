<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Consultation;
use App\Models\Client;
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

        $consultations = $query->paginate(15);
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
        $request->validate([
            'consultation_party' => 'required|in:' . implode(',', array_keys(config('master.consultation_parties'))),
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'inquirytype' => 'required|in:' . implode(',', array_keys(config('master.inquirytypes'))),
            'office_id' => 'required|in:' . implode(',', array_keys(config('master.offices_id'))),
            'status' => 'required|in:' . implode(',', array_keys(config('master.consultation_statuses'))),
        ]);
    
        Consultation::create([
            'client_id' => $request->client_id,
            'consultation_party' => $request->consultation_party,
            'title' => $request->title,
            'inquirytype' => $request->inquirytype,
            'office_id' => $request->office_id,
            'status' => $request->status,
        ]);

        return redirect()->route('consultation.index')->with('success', '相談を追加しました！');
    }

    // 相談詳細処理
    public function show(Consultation $consultation)
    {
        return view('consultation.show', compact('consultation'));
    }
    
    // 相談編集処理（あとで作成）

    // 相談削除処理
    public function destroy(Consultation $consultation)
    {
        $this->ensureIsAdmin();
        $consultation->delete();
        return redirect()->route('consultation.index')->with('success', '相談を削除しました！');
    }

}
