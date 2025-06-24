@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<h2 class="text-2xl font-bold mb-2 text-gray-800">顧問相談登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('advisory_consultation.store') }}" method="POST">
    @csrf

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">顧問相談情報</div>

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
                    クライアント・顧問契約
                </div>
                <!-- クライアント -->
                <div id="existing-client-area">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>クライアント検索</label>
                    @if (request('client_id'))
                        <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                        <input type="text"
                               value="{{ \App\Models\Client::find(request('client_id'))?->name_kanji ?? '（不明）' }}"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               readonly>
                    @else
                        <select name="client_id"
                                class="select-client w-full"
                                data-old-id="{{ old('client_id') }}"
                                data-old-text="{{ old('client_name_display') }}">
                            <option></option>
                        </select>
                    @endif
                    @errorText('client_id')
                </div>
                <!-- 顧問契約 -->
                <div id="existing-advisory-area">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>顧問契約検索</label>
                    @if (request('advisory_id'))
                        <input type="hidden" name="advisory_id" value="{{ request('advisory_id') }}">
                        <input type="text"
                               value="{{ \App\Models\AdvisoryContract::find(request('advisory_id'))?->title ?? '（不明）' }}"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               readonly>
                    @else
                        <select name="advisory_id"
                                class="select-advisory w-full"
                                data-old-id="{{ old('advisory_id') }}"
                                data-old-text="{{ old('advisory_contract_name_display') }}">
                            <option></option>
                        </select>
                    @endif
                    @errorText('advisory_id')
                </div>

                <div class="mt-0 p-4 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800">    
                    新規クライアントの場合は、まず
                    <a href="{{ route('client.create') }}" class="text-blue-600 underline font-semibold">こちらからクライアント登録</a>
                    を行い、その後この画面で再選択してください。
                </div>

                <div class="mt-0 p-4 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800">    
                    新規顧問契約の場合は、まず
                    <a href="{{ route('advisory.create') }}" class="text-blue-600 underline font-semibold">こちらから顧問契約登録</a>
                    を行い、その後この画面で再選択してください。
                </div>

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  基本情報
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
                <!-- ステータス -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>ステータス
                    </label>
                    <select name="status" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.advisory_consultations_statuses') as $key => $label)
                                @if (in_array($key, [1, 2])) {{-- 登録時に選べる値だけ --}}
                                    <option value="{{ $key }}" @selected(old('status') == $key)>{{ $label }}</option>
                                @endif
                        @endforeach
                    </select>
                    @errorText('status')
                </div>
                <!-- 相談概要 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談概要</label>
                    <textarea name="case_summary" rows="4" maxlength="1000"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('case_summary') }}</textarea>
                    @errorText('case_summary')
                </div>
                <!-- 特記事項 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                    <textarea name="special_notes" rows="4" maxlength="1000"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('special_notes') }}</textarea>
                    @errorText('special_notes')
                </div>
                 <!-- 利益相反（チェックボックス） -->
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="opponentconfliction" value="1"
                               @checked(old('opponentconfliction') == 1)>
                        <span class="ml-2">利益相反確認</span>
                    </label>
                    @errorText('opponentconfliction')
                </div>

                <!-- 相談開始日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談開始日</label>
                    <input type="date" name="consultation_start_date"
                           value="{{ old('consultation_start_date') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('consultation_start_date')
                </div>

                <!-- 小見出し：担当 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    担当
                </div>
                <!-- 取扱事務所 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        取扱事務所
                    </label>
                    <select name="office_id" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.offices_id') as $key => $label)
                            <option value="{{ $key }}" @selected(old('office_id') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('office_id')
                </div>
                <div></div>

                <!-- 弁護士 -->
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

                <!-- パラリーガル -->
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
            </div>    
        </div>
        <!-- ボタン -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('advisory_consultation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                一覧に戻る
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                登録する
            </button>
        </div>
    </form>
</div>
@endsection