<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\InsuranceCompany;
use Illuminate\Validation\Rule;

class InsuranceCompanyController extends Controller
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

    // 保険会社一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = InsuranceCompany::query();

        if ($request->filled('insurance_name')) {
            $query->where('insurance_name', 'like', '%' . $request->insurance_name . '%');
        }
        if ($request->filled('insurance_type')) {
            $query->where('insurance_type', $request->insurance_type);
        }

        $insurances = $query->paginate(15);
        return view('insurance.index', compact('insurances'));
    }

        // 保険会社追加画面
        public function create()
        {
            return view('insurance.create');
        }
    
        // 保険会社追加処理
        public function store(Request $request)
        {        
            $request->validate([
                'insurance_name' => 'required|string|max:255',
                'insurance_type' => 'required|in:' . implode(',', array_keys(config('master.insurance_types'))),
                'contactname' => 'nullable|string|max:255',
                'contactname2' => 'nullable|string|max:255',
                'phone_number'  => 'nullable|regex:/^[0-9]+$/|max:15',
                'phone_number2' => 'nullable|regex:/^[0-9]+$/|max:15',
                'email'         => 'nullable|email|max:255',
                'email2'        => 'nullable|email|max:255',
                'importantnotes' => 'nullable|string|max:255',
            ]);
        
            InsuranceCompany::create([
                'insurance_name' => $request->insurance_name,
                'insurance_type' => $request->insurance_type,
                'contactname' => $request->contactname,
                'contactname2' => $request->contactname2,
                'phone_number' => $request->phone_number,
                'phone_number2' => $request->phone_number2,
                'email' => $request->email,
                'email2' => $request->email2,
                'importantnotes' => $request->importantnotes,
            ]);
        
            return redirect()->route('insurance.index')->with('success', '保険会社を追加しました！');
        }
    
    // 保険会社詳細表示
    public function show(InsuranceCompany $insurance)
    {
        return view('insurance.show', compact('insurance'));
    }

    public function update(Request $request, InsuranceCompany $insurance)
    {    
        $request->validate([
            'insurance_name' => 'required|string|max:255',
            'insurance_type' => 'required|in:' . implode(',', array_keys(config('master.insurance_types'))),
            'contactname' => 'nullable|string|max:255',
            'contactname2' => 'nullable|string|max:255',
            'phone_number'  => 'nullable|regex:/^[0-9]+$/|max:15',
            'phone_number2' => 'nullable|regex:/^[0-9]+$/|max:15',
            'email'         => 'nullable|email|max:255',
            'email2'        => 'nullable|email|max:255',
            'importantnotes' => 'nullable|string|max:255',
        ]);
    
        $insurance->update([
            'insurance_name' => $request->insurance_name,
            'insurance_type' => $request->insurance_type,
            'contactname' => $request->contactname,
            'contactname2' => $request->contactname2,
            'phone_number' => $request->phone_number,
            'phone_number2' => $request->phone_number2,
            'email' => $request->email,
            'email2' => $request->email2,
            'importantnotes' => $request->importantnotes,
        ]);

        return redirect()->route('insurance.show', $insurance->id)->with('success', '保険会社情報を更新しました！');
    }

    // 保険会社削除
    public function destroy(InsuranceCompany $insurance)
    {
        try {
            $insurance->delete();
            return redirect()->route('insurance.index')->with('success', '保険会社を削除しました！');
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
