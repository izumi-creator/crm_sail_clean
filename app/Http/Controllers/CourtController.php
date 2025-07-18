<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Court;
use Illuminate\Validation\Rule; 

class CourtController extends Controller
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

    // 裁判所一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = Court::query();

        if ($request->filled('court_name')) {
            $query->where('court_name', 'like', '%' . $request->court_name . '%');
        }
        if ($request->filled('court_type')) {
            $query->where('court_type', $request->court_type);
        }

        $courts = $query->paginate(15);
        return view('court.index', compact('courts'));
    }

        // 裁判所追加画面
        public function create()
        {
            return view('court.create');
        }
    
        // 裁判所追加処理
        public function store(Request $request)
        {        
            $request->validate([
                'court_name' => ['required','string','max:255', Rule::unique('courts')],
                'court_type' => 'required|in:' . implode(',', array_keys(config('master.court_types'))),
                'postal_code' => 'nullable|regex:/^\d{3}-\d{4}$/',
                'location' => 'nullable|string|max:255',
                'phone_number'  => 'nullable|regex:/^[0-9]+$/|max:15',
                'importantnotes' => 'nullable|string|max:255',
            ]);
        
            Court::create([
                'court_name' => $request->court_name,
                'court_type' => $request->court_type,
                'postal_code' => $request->postal_code,
                'location' => $request->clocation,
                'phone_number' => $request->phone_number,
                'importantnotes' => $request->importantnotes,
            ]);
        
            return redirect()->route('court.index')->with('success', '裁判所を追加しました！');
        }

    // 裁判所詳細表示
    public function show(Court $court)
    {
        return view('court.show', compact('court'));
    }
    
    public function update(Request $request, Court $court)
    {
        $request->validate([
            'court_name' => ['required','string','max:255', Rule::unique('courts')->ignore($court->id)],
            'court_type' => 'required|in:' . implode(',', array_keys(config('master.court_types'))),
            'postal_code' => 'nullable|regex:/^\d{3}-\d{4}$/',
            'location' => 'nullable|string|max:255',
            'phone_number'  => 'nullable|regex:/^[0-9]+$/|max:15',
            'importantnotes' => 'nullable|string|max:255',
        ]);

        $court->update([
            'court_name' => $request->court_name,
            'court_type' => $request->court_type,
            'postal_code' => $request->postal_code,
            'location' => $request->location,
            'phone_number' => $request->phone_number,
            'importantnotes' => $request->importantnotes,
        ]);

        return redirect()->route('court.show', $court->id)->with('success', '裁判所を更新しました！');
    }
    // 裁判所削除処理
    public function destroy(Court $court)
    {
        try {
            $court->delete();
            return redirect()->route('court.index')->with('success', '裁判所を削除しました！');
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

    // 裁判所検索API
    public function search(Request $request)
    {
        $keyword = $request->input('q');

        $results = [];

        if ($keyword) {
            $results = Court::where('court_name', 'like', "%{$keyword}%")
                ->orWhere('location', 'like', "%{$keyword}%")
                ->select('id', 'court_name') // 表示する名前カラムに応じて調整
                ->limit(10)
                ->get()
                ->map(fn($court) => [
                    'id' => $court->id,
                    'text' => $court->court_name, // Select2の表示名
                ]);
        }

        return response()->json(['results' => $results]);
    }

}
