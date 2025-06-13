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

    // クライアント一覧（検索 + ページネーション）
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('name_kanji')) {
            $query->where('name_kanji', 'like', '%' . $request->name_kanji . '%');
        }
        if ($request->filled('name_kana')) {
            $query->where('name_kana', 'like', '%' . $request->name_kana . '%');
        }
        if ($request->filled('phone_number_search')) {
            $query->where(function ($q) use ($request) {
                $q->where('phone_number', 'like', '%' . $request->phone_number_search . '%')
                  ->orWhere('phone_number2', 'like', '%' . $request->phone_number_search . '%')
                  ->orWhere('first_contact_number', 'like', '%' . $request->phone_number_search . '%')
                  ->orWhere('second_contact_number', 'like', '%' . $request->phone_number_search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $request->phone_number_search . '%')
                  ->orWhere('home_phone_number', 'like', '%' . $request->phone_number_search . '%')
                  ->orWhere('contact_phone_number', 'like', '%' . $request->phone_number_search . '%')
                  ->orWhere('contact_mobile_number', 'like', '%' . $request->phone_number_search . '%')
                  ->orWhere('contact_home_phone_number', 'like', '%' . $request->phone_number_search . '%');
            });
        }
        if ($request->filled('email_search')) {
            $query->where(function ($q) use ($request) {
                $q->where('email1', 'like', '%' . $request->email_search . '%')
                  ->orWhere('email2', 'like', '%' . $request->email_search . '%')
                  ->orWhere('contact_email1', 'like', '%' . $request->email_search . '%')
                  ->orWhere('contact_email2', 'like', '%' . $request->email_search . '%');
            });
        }

        $clients = $query->paginate(15);
        return view('client.index', compact('clients'));
    }

    // クライアント追加画面
    public function create()
    {
        return view('client.create');
    }

    public function store(Request $request)
    {
        $clientType = $request->input('client_type');
    
        // 共通: client_type の妥当性確認
        $request->validate([
            'client_type' => 'required|in:' . implode(',', array_keys(config('master.client_types'))),
        ]);
    
        // クライアント種別によって個別のバリデーションルールを適用
        if ($clientType === '1') {
            // 個人用バリデーション
            $validated = $request->validate([
                'individual' => 'required|array',
                'individual.name_kanji' => 'required|string|max:255',
                'individual.name_kana' => 'required|string|max:255',
                'individual.last_name_kanji' => 'required|string|max:100',
                'individual.first_name_kanji' => 'required|string|max:155',
                'individual.last_name_kana' => 'required|string|max:100',
                'individual.first_name_kana' => 'required|string|max:155',
                'individual.birthday' => 'nullable|date',
                'individual.identification_document1' => 'nullable|in:' . implode(',', array_keys(config('master.identification_documents'))),
                'individual.identification_document2' => 'nullable|in:' . implode(',', array_keys(config('master.identification_documents'))),
                'individual.identification_document3' => 'nullable|in:' . implode(',', array_keys(config('master.identification_documents'))),
                'individual.mobile_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.first_contact_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.second_contact_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.email1' => 'nullable|email|max:255',
                'individual.email2' => 'nullable|email|max:255',
                'individual.home_phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.fax' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.not_home_contact' => 'nullable|in:' . implode(',', array_keys(config('master.not_home_contacts'))),
                'individual.address_postalcode' => 'nullable|string|max:10',
                'individual.contact_postalcode' => 'nullable|string|max:10',
                'individual.address_state' => 'nullable|string|max:10',
                'individual.contact_state' => 'nullable|string|max:10',
                'individual.address_city' => 'nullable|string|max:20',
                'individual.contact_city' => 'nullable|string|max:20',
                'individual.address_street' => 'nullable|string|max:100',
                'individual.contact_street' => 'nullable|string|max:100',
                'individual.address_name_kanji' => 'nullable|string|max:255',
                'individual.contact_name_kanji' => 'nullable|string|max:255',
                'individual.address_name_kana' => 'nullable|string|max:255',
                'individual.contact_name_kana' => 'nullable|string|max:255',
                'individual.contact_address_notes' => 'nullable|string|max:1000',
                'individual.send_newyearscard' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'individual.send_summergreetingcard' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'individual.send_office_news' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'individual.send_autocreation' => 'nullable|in:' . implode(',', array_keys(config('master.send_autocreations'))),
            ]);
            $data = $validated['individual'];

            // ✅ チェックボックス補完（未送信時は null → 0 に補正）
            $data['not_home_contact'] = $request->input('individual.not_home_contact', 0);
            $data['send_newyearscard'] = $request->input('individual.send_newyearscard', 0);
            $data['send_summergreetingcard'] = $request->input('individual.send_summergreetingcard', 0);
            $data['send_office_news'] = $request->input('individual.send_office_news', 0);
            $data['send_autocreation'] = $request->input('individual.send_autocreation', 0);
    
        } elseif ($clientType === '2') {
            // 法人用バリデーション
            $validated = $request->validate([
                'corporate' => 'required|array',
                'corporate.name_kanji' => 'required|string|max:255',
                'corporate.name_kana' => 'required|string|max:255',
                'corporate.phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.phone_number2' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.first_contact_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.second_contact_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.fax' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.email1' => 'nullable|email|max:255',
                'corporate.contact_last_name_kanji' => 'nullable|string|max:100',
                'corporate.contact_first_name_kanji' => 'nullable|string|max:155',
                'corporate.contact_last_name_kana' => 'nullable|string|max:100',
                'corporate.contact_first_name_kana' => 'nullable|string|max:155',
                'corporate.contact_phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.contact_mobile_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.contact_home_phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.contact_email1' => 'nullable|email|max:255',
                'corporate.contact_email2' => 'nullable|email|max:255',
                'corporate.contact_fax' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.address_postalcode' => 'nullable|string|max:10',
                'corporate.contact_postalcode' => 'nullable|string|max:10',
                'corporate.address_state' => 'nullable|string|max:10',
                'corporate.contact_state' => 'nullable|string|max:10',
                'corporate.address_city' => 'nullable|string|max:20',
                'corporate.contact_city' => 'nullable|string|max:20',
                'corporate.address_street' => 'nullable|string|max:100',
                'corporate.contact_street' => 'nullable|string|max:100',
                'corporate.address_name_kanji' => 'nullable|string|max:255',
                'corporate.contact_name_kanji' => 'nullable|string|max:255',
                'corporate.address_name_kana' => 'nullable|string|max:255',
                'corporate.contact_name_kana' => 'nullable|string|max:255',
                'corporate.contact_address_notes' => 'nullable|string|max:1000',
                'corporate.send_newyearscard' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'corporate.send_summergreetingcard' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'corporate.send_office_news' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'corporate.send_autocreation' => 'nullable|in:' . implode(',', array_keys(config('master.send_autocreations'))),
            ]);
            $data = $validated['corporate'];

            // ✅ チェックボックス補完（未送信時は null → 0 に補正）
            $data['send_newyearscard'] = $request->input('corporate.send_newyearscard', 0);
            $data['send_summergreetingcard'] = $request->input('corporate.send_summergreetingcard', 0);
            $data['send_office_news'] = $request->input('corporate.send_office_news', 0);
            $data['send_autocreation'] = $request->input('corporate.send_autocreation', 0);


        } else {
            abort(400, '無効なクライアント区分です');
        }
    
        // 共通項目
        $data['client_type'] = $clientType;
    
        // 登録処理
        Client::create($data);
    
        return redirect()->route('client.index')->with('success', 'クライアントを追加しました！');
    }

    // 詳細表示
    public function show(Client $client)
    {
        return view('client.show', compact('client'));
    }
    
    // 編集処理
    public function update(Request $request, Client $client)
    {
        // 共通：クライアント種別を取得（1 = 個人, 2 = 法人）
        $type = $client->client_type;
    
        // 個人バリデーション
        if ($type == 1) {
            $validated = $request->validate([
                'individual.name_kanji' => 'required|string|max:255',
                'individual.name_kana' => 'required|string|max:255',
                'individual.last_name_kanji' => 'required|string|max:100',
                'individual.first_name_kanji' => 'required|string|max:155',
                'individual.last_name_kana' => 'required|string|max:100',
                'individual.first_name_kana' => 'required|string|max:155',
                'individual.birthday' => 'nullable|date',
                'individual.identification_document1' => 'nullable|in:' . implode(',', array_keys(config('master.identification_documents'))),
                'individual.identification_document2' => 'nullable|in:' . implode(',', array_keys(config('master.identification_documents'))),
                'individual.identification_document3' => 'nullable|in:' . implode(',', array_keys(config('master.identification_documents'))),
                'individual.mobile_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.first_contact_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.second_contact_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.email1' => 'nullable|email|max:255',
                'individual.email2' => 'nullable|email|max:255',
                'individual.home_phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.fax' => 'nullable|regex:/^[0-9]+$/|max:15',
                'individual.not_home_contact' => 'nullable|in:' . implode(',', array_keys(config('master.not_home_contacts'))),
                'individual.address_postalcode' => 'nullable|string|max:10',
                'individual.contact_postalcode' => 'nullable|string|max:10',
                'individual.address_state' => 'nullable|string|max:10',
                'individual.contact_state' => 'nullable|string|max:10',
                'individual.address_city' => 'nullable|string|max:20',
                'individual.contact_city' => 'nullable|string|max:20',
                'individual.address_street' => 'nullable|string|max:100',
                'individual.contact_street' => 'nullable|string|max:100',
                'individual.address_name_kanji' => 'nullable|string|max:255',
                'individual.contact_name_kanji' => 'nullable|string|max:255',
                'individual.address_name_kana' => 'nullable|string|max:255',
                'individual.contact_name_kana' => 'nullable|string|max:255',
                'individual.contact_address_notes' => 'nullable|string|max:1000',
                // 他の個人用フィールドのバリデーションをここに追加
            ]);
        $data = $validated['individual'];
        $data['not_home_contact'] = $request->input('individual.not_home_contact', 0);
        $data['send_newyearscard'] = $request->input('individual.send_newyearscard', 0);
        $data['send_summergreetingcard'] = $request->input('individual.send_summergreetingcard', 0);
        $data['send_office_news'] = $request->input('individual.send_office_news', 0);
        $data['send_autocreation'] = $request->input('individual.send_autocreation', 0);

        // 法人バリデーション
        } elseif ($type == 2) {
            $validated = $request->validate([
                'corporate.name_kanji' => 'required|string|max:255',
                'corporate.name_kana' => 'required|string|max:255',
                'corporate.phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.phone_number2' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.first_contact_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.second_contact_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.fax' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.email1' => 'nullable|email|max:255',
                'corporate.contact_last_name_kanji' => 'nullable|string|max:100',
                'corporate.contact_first_name_kanji' => 'nullable|string|max:155',
                'corporate.contact_last_name_kana' => 'nullable|string|max:100',
                'corporate.contact_first_name_kana' => 'nullable|string|max:155',
                'corporate.contact_phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.contact_mobile_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.contact_home_phone_number' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.contact_email1' => 'nullable|email|max:255',
                'corporate.contact_email2' => 'nullable|email|max:255',
                'corporate.contact_fax' => 'nullable|regex:/^[0-9]+$/|max:15',
                'corporate.address_postalcode' => 'nullable|string|max:10',
                'corporate.contact_postalcode' => 'nullable|string|max:10',
                'corporate.address_state' => 'nullable|string|max:10',
                'corporate.contact_state' => 'nullable|string|max:10',
                'corporate.address_city' => 'nullable|string|max:20',
                'corporate.contact_city' => 'nullable|string|max:20',
                'corporate.address_street' => 'nullable|string|max:100',
                'corporate.contact_street' => 'nullable|string|max:100',
                'corporate.address_name_kanji' => 'nullable|string|max:255',
                'corporate.contact_name_kanji' => 'nullable|string|max:255',
                'corporate.address_name_kana' => 'nullable|string|max:255',
                'corporate.contact_name_kana' => 'nullable|string|max:255',
                'corporate.contact_address_notes' => 'nullable|string|max:1000',
                'corporate.send_newyearscard' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'corporate.send_summergreetingcard' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'corporate.send_office_news' => 'nullable|in:' . implode(',', array_keys(config('master.send_types'))),
                'corporate.send_autocreation' => 'nullable|in:' . implode(',', array_keys(config('master.send_autocreations'))),
            ]);
            $data = $validated['corporate'];
            $data['send_newyearscard'] = $request->input('corporate.send_newyearscard', 0);
            $data['send_summergreetingcard'] = $request->input('corporate.send_summergreetingcard', 0);
            $data['send_office_news'] = $request->input('corporate.send_office_news', 0);
            $data['send_autocreation'] = $request->input('corporate.send_autocreation', 0);
        }
        $client->update($data);
        return redirect()->route('client.show', $client->id)->with('success', 'クライアントを更新しました！');
    }

    // 削除処理
    public function destroy(Client $client)
    {
        $this->ensureIsAdmin();
        $client->delete();
        return redirect()->route('client.index')->with('success', 'クライアントを削除しました！');
    }

}
