@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">顧問相談詳細</h2>

    <!-- ✅ 上段：主要項目カード（個人／法人で出し分け） -->
    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <!-- 見出しバー -->
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-sm text-gray-100 mb-1">
                {{ $advisory_consultation->advisory_party == 1 ? '個人の顧問相談' : '法人の顧問相談' }}
            </div>
            <div class="text-xl font-bold">
                @if ($advisory_consultation->client)
                    <a href="{{ route('client.show', $advisory_consultation->client_id) }}" class="hover:underline">
                        {{ optional($advisory_consultation->client)->name_kanji }}（{{ optional($advisory_consultation->client)->name_kana }}）
                    </a>
                @else
                    （不明）
                @endif
            </div>
        </div>

        <!-- 内容エリア -->
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 px-6 py-4">
            @if ($advisory_consultation->advisory_party == 1)
                <!-- 個人クライアント用表示 -->
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->client)->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->client)->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス1:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->client)->email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス2:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->client)->email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->paralegal)->name ?: '&nbsp;' !!}</span>
                </div>
            @else
                <!-- 法人クライアント用表示 -->
                <div class="col-span-2">
                    <span class="font-semibold">取引先責任者名:</span>
                    <span class="ml-2">
                        {{ optional($advisory_consultation->client)->contact_last_name_kanji }}　{{ optional($advisory_consultation->client)->contact_first_name_kanji }}
                        （{{ optional($advisory_consultation->client)->contact_last_name_kana }}　{{ optional($advisory_consultation->client)->contact_first_name_kana }}）
                    </span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->client)->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->client)->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール1:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->client)->contact_email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール2:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->client)->contact_email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($advisory_consultation->paralegal)->name ?: '&nbsp;' !!}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- タブ切替ボタン -->
    <div class="mb-0 overflow-x-auto border-b border-gray-300 bg-gray-100 rounded-t">
        <div class="flex space-x-2 pt-2 px-6 w-fit">
            <button class="tab-btn active-tab px-4 py-2 text-sm font-bold text-sky-700 bg-white border-x border-t border-b-0 rounded-t" data-tab="tab-detail">
                詳細情報
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-relatedparty">
                関係者一覧（{{ $advisory_consultation->relatedParties->count() }}件）
            </button>
        </div>
    </div>


    <!-- ▼ 詳細情報タブ（今ある内容を全部この中に入れる） -->
    <div id="tab-detail" class="tab-content">

        <!-- 相談詳細カード -->
        <div class="p-6 border rounded-lg shadow bg-white">
            <!-- 上部ボタン -->
            <div class="flex justify-end space-x-2 mb-4">
                <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
                @if (auth()->user()->role_type == 1)
                <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
                @endif
            </div>

            <!-- ✅ 顧問契約情報の見出し＋内容を枠で囲む -->
            <div class="border border-gray-300 overflow-hidden">
                <!-- 見出し -->
                <div class="bg-sky-700 text-white px-4 py-2 font-bold border">顧問相談情報</div>
                <!-- 内容 -->
                <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                    <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                        基本情報
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">顧問契約：件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory_consultation->advisory)->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $advisory_consultation->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                            {!! $advisory_consultation->status ? config('master.advisory_consultations_statuses')[$advisory_consultation->status] : '&nbsp;' !!}
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">説明</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $advisory_consultation->case_summary }}</pre>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $advisory_consultation->special_notes }}</pre>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $advisory_consultation->opponentconfliction ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">利益相反確認</span>
                        </label>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>詳細情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談開始日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory_consultation->consultation_start_date ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談終了日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory_consultation->consultation_end_date ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">解決理由</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory_consultation->close_reason ? config('master.close_reasons')[$advisory_consultation->close_reason] : '&nbsp;' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>担当情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">取扱事務所</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory_consultation->office_id ? config('master.offices_id')[$advisory_consultation->office_id] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory_consultation->lawyer)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory_consultation->paralegal)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士2</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory_consultation->lawyer2)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル2</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory_consultation->paralegal2)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士3</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory_consultation->lawyer3)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル3</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory_consultation->paralegal3)->name ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ✅ 外枠の外に表示 -->
            <div class="relative mt-6 h-10">
               <!-- 左側：一覧に戻る -->
                <div class="absolute left-0">
                    <a href="{{ route('advisory_consultation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ▼ 関係者一覧タブ -->
    <div id="tab-relatedparty" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <div class="mb-4 flex justify-end space-x-2">
                <a href="{{ route('relatedparty.create', ['advisory_id' => $advisory_consultation->id]) }}"
                   class="bg-green-500 text-white px-4 py-2 rounded">
                    新規登録
                </a>
            </div>
            @if ($advisory_consultation->relatedParties->isEmpty())
                <p class="text-sm text-gray-500">関係者は登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                        <tr>
                            <th class="border p-2 w-1/12">ID</th>
                            <th class="border p-2 w-4/12">関係者名（漢字）</th>
                            <th class="border p-2 w-2/12">区分</th>
                            <th class="border p-2 w-2/12">分類</th>
                            <th class="border p-2 w-2/12">種別</th>
                            <th class="border p-2 w-3/12">立場</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($advisory_consultation->relatedParties as $relatedparty)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $relatedparty->id }}</td>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('relatedparty.show', $relatedparty->id) }}" class="text-blue-500">
                                    {{ $relatedparty->relatedparties_name_kanji }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.relatedparties_parties')[(string)$relatedparty->relatedparties_party] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.relatedparties_classes')[(string)$relatedparty->relatedparties_class] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.relatedparties_types')[(string)$relatedparty->relatedparties_type] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.relatedparties_positions')[(string)$relatedparty->relatedparties_position] ?? '未設定' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('advisory_consultation.update', $advisory_consultation->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">顧問相談編集</div>

                <!-- ✅ エラーボックスをgrid外に出す -->
                @if ($errors->any())
                <div class="p-6 pt-4 -mb-4 text-sm">
                    <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                        <ul class="list-disc pl-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                
                <!-- 入力フィールド -->
                <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                    <div class="col-span-2 bg-orange-300 py-2 px-6 -mx-6">
                         基本情報
                     </div>                     
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>件名</label>
                        <input type="text" name="title" value="{{ $advisory_consultation->title }}" class="w-full p-2 border rounded bg-white" required>
                        @errorText('title')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <select name="status" class="w-full p-2 border rounded bg-white" required>
                            <option value="">-- 選択してください --</option>
                            @foreach (config('master.advisory_consultations_statuses') as $key => $value)
                                <option value="{{ $key }}" {{ $advisory_consultation->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('status')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">相談概要</label>
                        <textarea name="case_summary" rows="3" class="mt-1 p-2 border rounded w-full bg-white required">{{ $advisory_consultation->case_summary }}</textarea>
                        @errorText('case_summary')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <textarea name="special_notes" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $advisory_consultation->special_notes }}</textarea>
                        @errorText('special_notes')
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="hidden" name="opponentconfliction" value="0">
                            <input type="checkbox" name="opponentconfliction" value="1"
                                {{ $advisory_consultation->opponentconfliction == 1 ? 'checked' : '' }}
                                class="form-checkbox text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">利益相反確認</span>
                        </label>
                        @errorText('opponentconfliction')
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>詳細情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>

                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談開始日</label>
                                    <input type="date" name="consultation_start_date" value="{{ $advisory_consultation->consultation_start_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('consultation_start_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談終了日</label>
                                    <input type="date" name="consultation_end_date" value="{{ $advisory_consultation->consultation_end_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('consultation_end_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">解決理由</label>
                                    <select name="close_reason" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.close_reasons') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory_consultation->close_reason == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('close_reason')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>担当情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">取扱事務所</label>
                                    <select name="office_id" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.offices_id') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory_consultation->office_id == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('office_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                                    <select name="lawyer_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory_consultation->lawyer_id }}"
                                            data-initial-text="{{ optional($advisory_consultation->lawyer)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
                                    <select name="paralegal_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory_consultation->paralegal_id }}"
                                            data-initial-text="{{ optional($advisory_consultation->paralegal)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士2</label>
                                    <select name="lawyer2_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory_consultation->lawyer2_id }}"
                                            data-initial-text="{{ optional($advisory_consultation->lawyer2)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer2_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル2</label>
                                    <select name="paralegal2_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory_consultation->paralegal2_id }}"
                                            data-initial-text="{{ optional($advisory_consultation->paralegal2)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal2_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士3</label>
                                    <select name="lawyer3_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory_consultation->lawyer3_id }}"
                                            data-initial-text="{{ optional($advisory_consultation->lawyer3)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer3_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル3</label>
                                    <select name="paralegal3_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory_consultation->paralegal3_id }}"
                                            data-initial-text="{{ optional($advisory_consultation->paralegal3)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal3_id')
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>クライアント・顧問契約（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント（編集不可）</label>
                                    <input type="text"
                                           class="w-full p-2 border rounded bg-gray-100 text-gray-700"
                                           value="{{ $advisory_consultation->client->name_kanji }}"
                                           disabled>
                                    <input type="hidden" name="client_id" value="{{ $advisory_consultation->client->id }}">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">顧問契約</label>
                                    <select name="advisory_id"
                                            class="select-advisory-edit w-full"
                                            data-initial-id="{{ $advisory_consultation->advisory->id }}"
                                            data-initial-text="{{ optional($advisory_consultation->advisory)->title }}">
                                    </select>
                                    <option></option>
                                    @errorText('advisory_id')
                                </div>
                                <div class="col-span-2 mt-0 p-4 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800">
                                    データ整合性の観点より、クライアントの変更は不可となります。
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('advisory_consultation.show', $advisory_consultation->id) }}"
                       class="px-4 py-2 bg-gray-300 text-black rounded min-w-[100px] text-center">
                       キャンセル
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 min-w-[100px]">
                        保存
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 削除モーダル -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-md">
            <!-- ヘッダー -->
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">顧問相談削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの顧問相談を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('advisory_consultation.destroy', $advisory_consultation->id) }}">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 text-black rounded">
                        キャンセル
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 min-w-[100px]">
                        削除
                    </button>
                </div>
            </form>
        </div>
    </div>
            
@endsection

@section('scripts')
@if ($errors->any())
<script>
    window.addEventListener('load', function () {
        document.getElementById('editModal')?.classList.remove('hidden');
        document.querySelectorAll('.accordion-content').forEach(content => {
            content.classList.remove('hidden');
            const icon = content.previousElementSibling?.querySelector('.accordion-icon');
            icon?.classList.add('rotate-180');
        });
    });
</script>
@endif

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ▽ タブ切り替え関数（初期用・クリック共通）
    function activateTab(tabId) {
        // ボタンのクラス切り替え
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove(
                'bg-white', 'text-sky-700', 'font-bold', 'border-x', 'border-t', 'border-b-0'
            );
            b.classList.add('text-gray-700');
        });

        const activeBtn = document.querySelector(`.tab-btn[data-tab="${tabId}"]`);
        if (activeBtn) {
            activeBtn.classList.add(
                'bg-white', 'text-sky-700', 'font-bold', 'border-x', 'border-t', 'border-b-0'
            );
            activeBtn.classList.remove('text-gray-700');
        }

        // コンテンツ切り替え
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        const targetContent = document.getElementById(tabId);
        if (targetContent) {
            targetContent.classList.remove('hidden');
        }
    }

    // ▼ 初期表示でURLのハッシュ（#tab-courtTask 等）に応じてタブを切り替える
    const hash = window.location.hash;
    if (hash) {
        const tabId = hash.replace('#', '');
        activateTab(tabId);
    } else {
        // ハッシュがない場合は最初のタブを有効にする
        const firstTab = document.querySelector('.tab-btn')?.dataset.tab;
        if (firstTab) {
            activateTab(firstTab);
        }
    }

    // ▽ タブクリックイベント登録
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const selectedTab = btn.dataset.tab;
            activateTab(selectedTab);

            // ハッシュも更新（履歴に残る）
            history.replaceState(null, null, '#' + selectedTab);
        });
    });
    
    // ▽ 1. アコーディオン制御  
    const toggles = document.querySelectorAll('.accordion-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.accordion-icon');
            if (content && content.classList.contains('accordion-content')) {
                content.classList.toggle('hidden');
                icon?.classList.toggle('rotate-180');
            }
        });
    });
});
</script>
@endsection