<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Validation\Rule;

class ClientController extends Controller
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
        $query = Client::query();

        if ($request->filled('name_kanji')) {
            $query->where('name_kanji', 'like', '%' . $request->name_kanji . '%');
        }
        if ($request->filled('name_kana')) {
            $query->where('name_kana', 'like', '%' . $request->name_kana . '%');
        }
        if ($request->filled('phone_number')) {
            $query->where('phone_number', 'like', '%' . $request->phone_number . '%');
        }
        if ($request->filled('email1')) {
            $query->where('email1', 'like', '%' . $request->email1 . '%');
        }

        $clients = $query->paginate(15);
        return view('client.index', compact('clients'));
    }

}
