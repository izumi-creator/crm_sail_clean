@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">関係者登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('relatedparty.store') }}" method="POST">
    @csrf

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">関係者情報</div>

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

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    区分・分類
                </div>
                <!-- 区分 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>区分
                    </label>
                        <select name="relatedparties_party" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.relatedparties_parties') as $key => $label)
                            <option value="{{ $key }}" @selected(old('relatedparties_party') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('relatedparties_party')
                </div>
                <!-- 分類 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>分類
                    </label>
                        <select name="relatedparties_class" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.relatedparties_classes') as $key => $label)
                            <option value="{{ $key }}" @selected(old('relatedparties_class') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('relatedparties_class')
                </div>
                <!-- 種別 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>種別
                    </label>
                        <select name="relatedparties_type" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.relatedparties_types') as $key => $label)
                            <option value="{{ $key }}" @selected(old('relatedparties_type') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('relatedparties_type')
                </div>
                <!-- 立場 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>立場
                    </label>
                        <select name="relatedparties_position" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.relatedparties_positions') as $key => $label)
                            <option value="{{ $key }}" @selected(old('relatedparties_position') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('relatedparties_position')
                </div>
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  説明  
                </div>
                <!-- 立場詳細 -->
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">立場詳細</label>
                    <input type="text" name="relatedparties_position_details" value="{{ old('relatedparties_position_details') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('relatedparties_position_details')
                </div>
                <!-- 説明 -->
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">説明</label>
                    <input type="text" name="relatedparties_explanation" value="{{ old('relatedparties_explanation') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('relatedparties_explanation')
                </div>
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  詳細情報
                </div>
                <!-- 関係者名（漢字） -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>関係者名（漢字）
                    </label>
                    <input type="text" name="relatedparties_name_kanji" value="{{ old('relatedparties_name_kanji') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('relatedparties_name_kanji')
                </div>
                <!-- 関係者名（ふりがな） -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">関係者名（ふりがな）</label>
                    <input type="text" name="relatedparties_name_kana" value="{{ old('relatedparties_name_kana') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('relatedparties_name_kana')
                </div>
                <!-- 携帯 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">携帯</label>
                    <input type="text" name="mobile_number" value="{{ old('mobile_number') }}"
                            placeholder="ハイフンなしで入力（例: 09012345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('mobile_number')
                </div>
                <!-- 電話 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">電話</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number')
                </div>
                <!-- 電話2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">電話2</label>
                    <input type="text" name="phone_number2" value="{{ old('phone_number2') }}"
                           placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number2')
                </div>
                <!-- FAX -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">FAX</label>
                    <input type="text" name="fax" value="{{ old('fax') }}"
                           placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('fax')
                </div>
                <!-- メール -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">メール</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('email')
                </div>
                <!-- メール2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">メール2</label>
                    <input type="email" name="email2" value="{{ old('email2') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('email2')
                </div>
                <!-- 郵便番号 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">郵便番号</label>
                    <input type="text" name="relatedparties_postcode" value="{{ old('relatedparties_postcode') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('relatedparties_postcode')
                </div>
                <!-- 住所 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">住所</label>
                    <input type="text" name="relatedparties_address" value="{{ old('relatedparties_address') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('relatedparties_address')
                </div>
                <!-- 住所2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">住所2</label>
                    <input type="text" name="relatedparties_address2" value="{{ old('relatedparties_address2') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('relatedparties_address2')
                </div>
                <!-- 勤務先 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">勤務先</label>
                    <input type="text" name="placeofwork" value="{{ old('placeofwork') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('placeofwork')
                </div>
                <!-- 担当者名（漢字） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当者名（漢字）</label>
                    <input type="text" name="manager_name_kanji" value="{{ old('manager_name_kanji') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('manager_name_kanji')
                </div>
                <!-- 担当者名（ふりがな） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当者名（ふりがな）</label>
                    <input type="text" name="manager_name_kana" value="{{ old('manager_name_kana') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('manager_name_kana')
                </div>
                <!-- 役職 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">役職</label>
                    <input type="text" name="manager_post" value="{{ old('manager_post') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('manager_post')
                </div>
                <!-- 部署 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">部署</label>
                    <input type="text" name="manager_department" value="{{ old('manager_department') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('manager_department')
                </div>
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  関係先
                </div>
                <!-- クライアントID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">クライアントID</label>
                    <input type="text" name="client_id" value="{{ old('client_id') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('client_id')
                </div>
                <!-- 相談（select2連携：相談詳細画面から来たときは固定） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談：件名</label>
                
                    @if (request('consultation_id'))
                        {{-- 相談詳細画面から遷移：hidden + readonly表示 --}}
                        <input type="hidden" name="consultation_id" value="{{ request('consultation_id') }}">
                        <input type="text"
                               value="{{ \App\Models\Consultation::find(request('consultation_id'))?->title ?? '（不明）' }}"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               readonly>
                    @else
                        {{-- 通常時：select2で検索して選択 --}}
                      <select name="consultation_id"
                            class="select-consultation w-full"
                            data-old-id="{{ old('consultation_id') }}"
                            data-old-text="{{ old('consultation_name_display') }}"> 
                            <option></option>
                    </select>

                    @endif
                    
                    @errorText('consultation_id')
                </div>
                <!-- 受任案件ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">受任案件ID</label>
                    <input type="text" name="business_id" value="{{ old('business_id') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('business_id')
                </div>
                <!-- 顧問相談ID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">顧問相談ID</label>
                    <input type="text" name="advisory_id" value="{{ old('advisory_id') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('advisory_id')
                </div>
            </div>
        </div>
        <!-- ボタン -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('relatedparty.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                一覧に戻る
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                登録する
            </button>
        </div>
    </form>
</div>
@endsection