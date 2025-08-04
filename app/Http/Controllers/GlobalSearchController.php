<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\RelatedParty;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $rawKeyword = $request->input('keyword');
        $keyword = str_replace([' ', '　'], '', $rawKeyword);

        if ($request->has('keyword') && empty($keyword)) {
            return view('global_search.index', [
                'keyword' => '',
                'clientsByName' => collect(),
                'clientsByContact' => collect(),
                'relatedByName' => collect(),
                'relatedByManager' => collect(),
                'error' => '検索キーワードを入力してください。',
            ]);
        }

        // クライアント名でヒット（name_kanji / name_kana）
        $clientsByName = Client::where(function ($query) use ($keyword) {
            $query->whereRaw("REPLACE(REPLACE(name_kanji, ' ', ''), '　', '') LIKE ?", ["%{$keyword}%"])
                  ->orWhereRaw("REPLACE(REPLACE(name_kana, ' ', ''), '　', '') LIKE ?", ["%{$keyword}%"]);
        })->get();

        // 責任者名でヒット（contact_姓+名）
        $clientsByContact = Client::where(function ($query) use ($keyword) {
            $query->orWhereRaw("REPLACE(REPLACE(CONCAT(contact_last_name_kanji, contact_first_name_kanji), ' ', ''), '　', '') LIKE ?", ["%{$keyword}%"])
                  ->orWhereRaw("REPLACE(REPLACE(CONCAT(contact_last_name_kana, contact_first_name_kana), ' ', ''), '　', '') LIKE ?", ["%{$keyword}%"]);
        })->get();

        // 関係者名でヒット（名前）
        $relatedByName = RelatedParty::where(function ($query) use ($keyword) {
            $query->whereRaw("REPLACE(REPLACE(relatedparties_name_kanji, ' ', ''), '　', '') LIKE ?", ["%{$keyword}%"])
                  ->orWhereRaw("REPLACE(REPLACE(relatedparties_name_kana, ' ', ''), '　', '') LIKE ?", ["%{$keyword}%"]);
        })->get();

        // 担当者名でヒット（manager_姓+名）
        $relatedByManager = RelatedParty::where(function ($query) use ($keyword) {
            $query->orWhereRaw("REPLACE(REPLACE(CONCAT(manager_name_kanji, manager_name_kana), ' ', ''), '　', '') LIKE ?", ["%{$keyword}%"]);
        })->get();

        return view('global_search.index', [
            'keyword' => $rawKeyword, // フォームには元の入力を保持
            'clientsByName' => $clientsByName,
            'clientsByContact' => $clientsByContact,
            'relatedByName' => $relatedByName,
            'relatedByManager' => $relatedByManager,
        ]);
    }
}
