@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif
@if (session('debug_participants'))
    <div class="bg-gray-100 p-2 mb-2 rounded text-sm">
        <strong>participants:</strong>
        <pre>{{ print_r(session('debug_participants'), true) }}</pre>
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">相談登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('consultation.store') }}" method="POST">
    @csrf

    <input type="hidden" name="redirect_url" value="{{ request('redirect_url') }}">

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">相談情報</div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                        <ul class="list-disc pl-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <!-- 入力フィールド -->
            <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                <!-- 小見出し：クライアント -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    クライアント
                </div>

                    @if (request('client_id'))
                        {{-- クライアント詳細から遷移した場合：ラジオ非表示、クライアント固定 --}}
                        <input type="hidden" name="client_mode" value="existing">
                        <input type="hidden" name="client_id" value="{{ request('client_id') }}">

                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント</label>
                            <input type="text"
                                   value="{{ \App\Models\Client::find(request('client_id'))?->name_kanji ?? '（不明）' }}"
                                   class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                                   readonly>
                            @errorText('client_id')
                        </div>
                    @else
                        {{-- 通常表示（既存／新規選択可） --}}
                        <div class="col-span-2">
                            <label class="font-semibold text-gray-700">クライアントの種別</label>
                            <div class="flex gap-4 mt-1">
                                <label><input type="radio" name="client_mode" value="existing" {{ old('client_mode', 'existing') == 'existing' ? 'checked' : '' }}> 既存クライアント</label>
                                <label><input type="radio" name="client_mode" value="new" {{ old('client_mode') == 'new' ? 'checked' : '' }}> 新規クライアント</label>
                            </div>
                        </div>
                    
                        {{-- 既存クライアント（Select2） --}}
                        <div id="existing-client-area">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント検索</label>
                            <select name="client_id"
                                    class="select-client w-full"
                                    data-old-id="{{ old('client_id') }}"
                                    data-old-text="{{ old('client_name_display') }}">
                                <option></option>
                            </select>
                            @errorText('client_id')
                        </div>

                        {{-- 新規クライアント（Select2） --}}
                        <div id="new-client-area" class="hidden col-span-2 grid grid-cols-2 gap-6">
                            {{-- 個人 or 法人 ラジオボタン --}}
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">種別</label>
                                <div class="flex gap-4 mb-2">
                                    <label><input type="radio" name="client_type" value="individual"> 個人</label>
                                    <label><input type="radio" name="client_type" value="corporation"> 法人</label>
                                </div>
                            </div>

                            {{-- 個人フォーム --}}
                            <div id="individual-form" class="col-span-2 grid grid-cols-2 gap-6 hidden">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                                            <span class="text-red-500">*</span>クライアント名（漢字） 
                                        </label>
                                            <input type="text" name="individual[name_kanji]" value="{{ old('individual.name_kanji') }}"
                                               placeholder="姓・名の入力で自動反映"
                                               class="w-full p-2 border rounded bg-gray-100 text-gray-700 cursor-not-allowed" readonly>
                                            @errorText('individual.name_kanji') 
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                                            <span class="text-red-500">*</span>クライアント名（ふりがな） 
                                        </label>
                                            <input type="text" name="individual[name_kana]" value="{{ old('individual.name_kana') }}"
                                                placeholder="姓・名の入力で自動反映"
                                               class="w-full p-2 border rounded bg-gray-100 text-gray-700 cursor-not-allowed" readonly>
                                            @errorText('individual.name_kana')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                                            <span class="text-red-500">*</span>姓（漢字）</label>
                                        <input type="text" name="individual[last_name_kanji]" value="{{ old('individual.last_name_kanji') }}" 
                                            class="w-full p-2 border rounded bg-white">
                                            @errorText('individual.last_name_kanji')                    
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                                            <span class="text-red-500">*</span>名（漢字）</label>
                                        <input type="text" name="individual[first_name_kanji]" value="{{ old('individual.first_name_kanji') }}" 
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.first_name_kanji')                    
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                                            <span class="text-red-500">*</span>姓（かな）</label>
                                        <input type="text" name="individual[last_name_kana]" value="{{ old('individual.last_name_kana') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.last_name_kana')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                                            <span class="text-red-500">*</span>名（かな）</label>
                                        <input type="text" name="individual[first_name_kana]" value="{{ old('individual.first_name_kana') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.first_name_kana')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">電話番号（第一連絡先）</label>
                                        <input type="text" name="individual[first_contact_number]" value="{{ old('individual.first_contact_number') }}"
                                               placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.first_contact_number')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">メールアドレス1</label>
                                        <input type="email" name="individual[email1]" value="{{ old('individual.email1') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.email1')
                                    </div>
                            </div>

                            {{-- 法人フォーム --}}
                            <div id="corporation-form" class="col-span-2 grid grid-cols-2 gap-6 hidden">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                                            <span class="text-red-500">*</span>クライアント名（漢字） 
                                        </label>
                                            <input type="text" name="corporate[name_kanji]" value="{{ old('corporate.name_kanji') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.name_kanji')
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                                            <span class="text-red-500">*</span>クライアント名（ふりがな） 
                                        </label>
                                            <input type="text" name="corporate[name_kana]" value="{{ old('corporate.name_kana') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.name_kana')
                                    </div>
                                    <!-- 取引先責任者_姓（漢字）contact_last_name_kanji -->
                                    <div>
                                        <label class="block font-semibold mb-1">取引先責任者_姓（漢字）</label>
                                        <input type="text" name="corporate[contact_last_name_kanji]" value="{{ old('corporate.contact_last_name_kanji') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_last_name_kanji')
                                    </div>
                                    <!-- 取引先責任者_名（漢字）contact_first_name_kanji -->
                                    <div>
                                        <label class="block font-semibold mb-1">取引先責任者_名（漢字）</label>
                                        <input type="text" name="corporate[contact_first_name_kanji]" value="{{ old('corporate.contact_first_name_kanji') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_first_name_kanji')
                                    </div>
                                    <!-- 取引先責任者_姓（ふりがな）contact_last_name_kana -->
                                    <div>
                                        <label class="block font-semibold mb-1">取引先責任者_姓（ふりがな）</label>
                                        <input type="text" name="corporate[contact_last_name_kana]" value="{{ old('corporate.contact_last_name_kana') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_last_name_kana')
                                    </div>
                                    <!-- 取引先責任者_名（ふりがな）contact_first_name_kana -->
                                    <div>
                                        <label class="block font-semibold mb-1">取引先責任者_名（ふりがな）</label>
                                        <input type="text" name="corporate[contact_first_name_kana]" value="{{ old('corporate.contact_first_name_kana') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_first_name_kana')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">電話番号（第一連絡先）</label>
                                        <input type="text" name="corporate[first_contact_number]" value="{{ old('corporate.first_contact_number') }}"
                                               placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.first_contact_number')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">メールアドレス</label>
                                        <input type="email" name="corporate[email1]" value="{{ old('corporate.email1') }}"
                                               class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.email1')
                                    </div>
                            </div>
                        </div>
                    @endif
                <!-- 小見出し：相談 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    相談
                </div>
                <!-- 件名 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>件名
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('title')
                </div>
                <!-- 問い合せ形態 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>問い合せ形態
                    </label>
                    <select name="inquirytype" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.inquirytypes') as $key => $label)
                            <option value="{{ $key }}" @selected(old('inquirytype') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('inquirytype')
                </div>
                <!-- ステータス -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>ステータス
                    </label>
                    <select name="status" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.consultation_statuses') as $key => $label)
                                @if (in_array($key, [1, 2])) {{-- 登録時に選べる値だけ --}}
                                    <option value="{{ $key }}" @selected(old('status') == $key)>{{ $label }}</option>
                                @endif
                        @endforeach
                    </select>
                    @errorText('status')
                </div>

                <!-- 小見出し：担当 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    担当
                </div>
                <!-- 取扱事務所 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>取扱事務所
                    </label>
                    <select name="office_id" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.offices_id') as $key => $label)
                            <option value="{{ $key }}" @selected(old('office_id') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('office_id')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">GoogleDriveフォルダID</label>
                    <input type="text" name="folder_id" 
                    placeholder="例：1A2B3C4D5E6F7G8H9I0J"
                    value="{{ old('folder_id') }}" class="w-full p-2 border rounded bg-white">
                    @errorText('folder_id')
                </div>

                {{-- 弁護士 --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        弁護士（名前で検索）
                    </label>
                    <select name="lawyer_id"
                            class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('lawyer_id') }}"
                            data-old-text="{{ old('lawyer_name_display') }}">
                            <option></option>
                    </select>
                    @errorText('lawyer_id')
                </div>

                {{-- パラリーガル --}}
                <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-1">
                        パラリーガル（名前で検索）
                    </label>
                    <select name="paralegal_id" class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('paralegal_id') }}"
                            data-old-text="{{ old('paralegal_name_display') }}">
                        <option></option>
                    </select>
                    @errorText('paralegal_id')
                </div>

                <!-- 小見出し：関係者 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    相手先（関係者）
                </div>

                <!-- チェックで関係者1を表示 -->
                <div class="col-span-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="relatedparty-toggle-1" class="mr-2"> 相手先1を登録する
                    </label>
                </div>

                <!-- 関係者1 入力欄 -->
                <div id="participant1-form" class="col-span-2 grid grid-cols-2 gap-6 hidden">
                    <!-- 区分 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>区分
                        </label>
                        <select name="participants[0][party]" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_parties') as $key => $label)
                                <option value="{{ $key }}" @selected(old('participants.0.party') == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('participants.0.party')
                    </div>                
                    <!-- 分類 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>分類
                        </label>
                        <select name="participants[0][class]" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_classes') as $key => $label)
                                @if (in_array($key, [2])) {{-- 登録時に選べる値だけ --}}
                                    <option value="{{ $key }}" @selected(old('participants.0.class') == $key)>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                        @errorText('participants.0.class')
                    </div>                
                    <!-- 種別 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>種別
                        </label>
                        <select name="participants[0][type]" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_types') as $key => $label)
                                <option value="{{ $key }}" @selected(old('participants.0.type') == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('participants.0.type')
                    </div>                
                    <!-- 立場 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>立場
                        </label>
                        <select name="participants[0][position]" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_positions') as $key => $label)
                                <option value="{{ $key }}" @selected(old('participants.0.position') == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('participants.0.position')
                    </div>                
                    <!-- 関係者名 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>関係者名（漢字）
                        </label>
                        <input type="text" name="participants[0][name_kanji]" value="{{ old('participants.0.name_kanji') }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('participants.0.name_kanji')
                    </div>               
                    <!-- 担当者名（漢字） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当者名（漢字）</label>
                        <input type="text" name="participants[0][manager_name_kanji]" value="{{ old('participants.0.manager_name_kanji') }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('participants.0.manager_name_kanji')
                    </div>                
                    <!-- 担当者名（ふりがな） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当者名（ふりがな）</label>
                        <input type="text" name="participants[0][manager_name_kana]" value="{{ old('participants.0.manager_name_kana') }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('participants.0.manager_name_kana')
                    </div>
                </div>

                <!-- 関係者2の追加チェックボックス -->
                <div class="col-span-2 hidden" id="toggle-participant2-wrapper">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="relatedparty-toggle-2" class="mr-2"> 相手先2も登録する
                    </label>
                </div>

                <!-- 関係者2 入力欄 -->
                <div id="participant2-form" class="col-span-2 grid grid-cols-2 gap-6 hidden">
                    <!-- 区分 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>区分
                        </label>
                        <select name="participants[1][party]" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_parties') as $key => $label)
                                <option value="{{ $key }}" @selected(old('participants.1.party') == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('participants.1.party')
                    </div>                
                    <!-- 分類 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>分類
                        </label>
                        <select name="participants[1][class]" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_classes') as $key => $label)
                                @if (in_array($key, [2])) {{-- 登録時に選べる値だけ --}}
                                    <option value="{{ $key }}" @selected(old('participants.1.class') == $key)>{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                        @errorText('participants.1.class')
                    </div>                
                    <!-- 種別 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>種別
                        </label>
                        <select name="participants[1][type]" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_types') as $key => $label)
                                <option value="{{ $key }}" @selected(old('participants.1.type') == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('participants.1.type')
                    </div>                
                    <!-- 立場 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>立場
                        </label>
                        <select name="participants[1][position]" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_positions') as $key => $label)
                                <option value="{{ $key }}" @selected(old('participants.1.position') == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('participants.1.position')
                    </div>                
                    <!-- 関係者名 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>関係者名（漢字）
                        </label>
                        <input type="text" name="participants[1][name_kanji]" value="{{ old('participants.1.name_kanji') }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('participants.1.name_kanji')
                    </div>               
                    <!-- 担当者名（漢字） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当者名（漢字）</label>
                        <input type="text" name="participants[1][manager_name_kanji]" value="{{ old('participants.1.manager_name_kanji') }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('participants.1.manager_name_kanji')
                    </div>                
                    <!-- 担当者名（ふりがな） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当者名（ふりがな）</label>
                        <input type="text" name="participants[1][manager_name_kana]" value="{{ old('participants.1.manager_name_kana') }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('participants.1.manager_name_kana')
                    </div>
                </div>
            </div>
        </div>
        <!-- ボタン -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('consultation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                一覧に戻る
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                登録する
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 新規／既存の切り替え
    document.querySelectorAll('input[name="client_mode"]').forEach((radio) => {
      radio.addEventListener('change', function () {
        const isNew = this.value === 'new';
        document.getElementById('existing-client-area').classList.toggle('hidden', isNew);
        document.getElementById('new-client-area').classList.toggle('hidden', !isNew);
      });
    }); 
    // ▼ 個人／法人 切り替え時の表示制御
    const radioButtons = document.querySelectorAll('input[name="client_type"]');
    const individualForm = document.getElementById('individual-form');
    const corporationForm = document.getElementById('corporation-form');

    function updateClientTypeView() {
        const selected = document.querySelector('input[name="client_type"]:checked');
        if (!selected) return;
    
        const isIndividual = selected.value === 'individual';
    
        // 表示制御
        individualForm.classList.toggle('hidden', !isIndividual);
        corporationForm.classList.toggle('hidden', isIndividual);
    
        // 入力無効化制御（ここがポイント！）
        document.querySelectorAll('#individual-form input, #individual-form select, #individual-form textarea')
            .forEach(el => el.disabled = !isIndividual);
    
        document.querySelectorAll('#corporation-form input, #corporation-form select, #corporation-form textarea')
            .forEach(el => el.disabled = isIndividual);
    }

    // 初期化（ページ表示直後）
    updateClientTypeView();

    // イベントリスナー設定
    radioButtons.forEach((radio) => {
        radio.addEventListener('change', updateClientTypeView);
    });

    // ▼ フルネーム補完（個人）
    const lastNameKanji = document.querySelector('input[name="individual[last_name_kanji]"]');
    const firstNameKanji = document.querySelector('input[name="individual[first_name_kanji]"]');
    const nameKanji = document.querySelector('input[name="individual[name_kanji]"]');   
    const lastNameKana = document.querySelector('input[name="individual[last_name_kana]"]');
    const firstNameKana = document.querySelector('input[name="individual[first_name_kana]"]');
    const nameKana = document.querySelector('input[name="individual[name_kana]"]'); 
    function updateFullNameKanji() {
        if (!lastNameKanji || !firstNameKanji || !nameKanji) return;
        if (!lastNameKanji.value && !firstNameKanji.value) {
            nameKanji.value = '';
        } else {
            nameKanji.value = `${lastNameKanji.value}　${firstNameKanji.value}`.trim();
        }
    }   
    function updateFullNameKana() {
        if (!lastNameKana || !firstNameKana || !nameKana) return;
        if (!lastNameKana.value && !firstNameKana.value) {
            nameKana.value = '';
        } else {
            nameKana.value = `${lastNameKana.value}　${firstNameKana.value}`.trim();
        }
    }   
    if (nameKanji && nameKanji.value === '') updateFullNameKanji();
    if (nameKana && nameKana.value === '') updateFullNameKana();    
    [lastNameKanji, firstNameKanji].forEach(el => el?.addEventListener('input', updateFullNameKanji));
    [lastNameKana, firstNameKana].forEach(el => el?.addEventListener('input', updateFullNameKana));

    // ▼ 関係者表示の制御（STEP3）
    const toggle1 = document.getElementById('relatedparty-toggle-1');
    const toggle2 = document.getElementById('relatedparty-toggle-2');
    const form1 = document.getElementById('participant1-form');
    const form2 = document.getElementById('participant2-form');
    const toggle2Wrapper = document.getElementById('toggle-participant2-wrapper'); // ← 追加！

    // 初期化
    if (toggle1 && form1) {
        toggle1.addEventListener('change', function () {
            const checked = this.checked;

            // フォーム1の表示・活性制御
            form1.classList.toggle('hidden', !checked);
            form1.querySelectorAll('input, select, textarea').forEach(el => el.disabled = !checked);

            // フォーム2の表示制御（1が外れたら2も閉じる）
            if (checked) {
                toggle2Wrapper.classList.remove('hidden'); // ← ここがポイント！
            } else {
                if (toggle2) toggle2.checked = false;
                if (form2) {
                    form2.classList.add('hidden');
                    form2.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);
                }
                toggle2Wrapper.classList.add('hidden');
            }
        });
    }

    if (toggle2 && form2) {
        toggle2.addEventListener('change', function () {
            const checked = this.checked;
            form2.classList.toggle('hidden', !checked);
            form2.querySelectorAll('input, select, textarea').forEach(el => el.disabled = !checked);
        });
    }

    });
</script>

@endsection