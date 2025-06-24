@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">顧問契約詳細</h2>

    <!-- ✅ 上段：主要項目カード（個人／法人で出し分け） -->
    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <!-- 見出しバー -->
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-sm text-gray-100 mb-1">
                {{ $advisory->advisory_party == 1 ? '個人の顧問契約' : '法人の顧問契約' }}
            </div>
            <div class="text-xl font-bold">
                @if ($advisory->client)
                    <a href="{{ route('client.show', $advisory->client_id) }}" class="hover:underline">
                        {{ optional($advisory->client)->name_kanji }}（{{ optional($advisory->client)->name_kana }}）
                    </a>
                @else
                    （不明）
                @endif
            </div>
        </div>

        <!-- 内容エリア -->
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 px-6 py-4">
            @if ($advisory->advisory_party == 1)
                <!-- 個人クライアント用表示 -->
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! optional($advisory->client)->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! optional($advisory->client)->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス1:</span>
                    <span class="ml-2">{!! optional($advisory->client)->email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス2:</span>
                    <span class="ml-2">{!! optional($advisory->client)->email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($advisory->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($advisory->paralegal)->name ?: '&nbsp;' !!}</span>
                </div>
            @else
                <!-- 法人クライアント用表示 -->
                <div class="col-span-2">
                    <span class="font-semibold">取引先責任者名:</span>
                    <span class="ml-2">
                        {{ optional($advisory->client)->contact_last_name_kanji }}　{{ optional($advisory->client)->contact_first_name_kanji }}
                        （{{ optional($advisory->client)->contact_last_name_kana }}　{{ optional($advisory->client)->contact_first_name_kana }}）
                    </span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! optional($advisory->client)->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! optional($advisory->client)->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール1:</span>
                    <span class="ml-2">{!! optional($advisory->client)->contact_email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール2:</span>
                    <span class="ml-2">{!! optional($advisory->client)->contact_email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($advisory->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($advisory->paralegal)->name ?: '&nbsp;' !!}</span>
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
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-advisory_consultations">
                顧問相談一覧（{{ $advisory->advisoryConsultation->count() }}件）
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-documents">
                会計一覧（0件）
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
                <div class="bg-sky-700 text-white px-4 py-2 font-bold border">顧問契約情報</div>
                <!-- 内容 -->
                <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                    <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                        基本情報
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $advisory->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                            {!! $advisory->status ? config('master.advisory_contracts_statuses')[$advisory->status] : '&nbsp;' !!}
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">説明</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $advisory->explanation }}</pre>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $advisory->special_notes }}</pre>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>詳細情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">契約開始日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->advisory_start_date ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">契約終了日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->advisory_end_date ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">顧問料月額</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->amount_monthly !== null ? '¥' . number_format($advisory->amount_monthly) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">契約期間（月）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $advisory->contract_term_monthly ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">初回相談日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->consultation_firstdate ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払区分</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->payment_category ? config('master.payment_categories')[$advisory->payment_category] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">自動引落番号</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->adviser_fee_auto ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払方法</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->payment_method ? config('master.payment_methods')[$advisory->payment_method] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">引落依頼額</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->withdrawal_request_amount !== null ? '¥' . number_format($advisory->withdrawal_request_amount) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">引落内訳</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->withdrawal_breakdown ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">引落更新日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->withdrawal_update_date ?: '&nbsp;' !!}
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
                                        {!! $advisory->office_id ? config('master.offices_id')[$advisory->office_id] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory->lawyer)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory->paralegal)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士2</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory->lawyer2)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル2</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory->paralegal2)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士3</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory->lawyer3)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル3</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($advisory->paralegal3)->name ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>ソース（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">ソース</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->source ? config('master.routes')[$advisory->source] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">ソース（詳細）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->source_detail ? config('master.routedetails')[$advisory->source_detail] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者その他</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $advisory->introducer_others ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>交際情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">お中元・お歳暮</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->gift ? config('master.gifts')[$advisory->gift] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">年賀状</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->newyearscard ? config('master.newyearscards')[$advisory->newyearscard] : '&nbsp;' !!}
                                    </div>
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
                    <a href="{{ route('advisory.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ▼ 顧問相談一覧タブ -->
    <div id="tab-advisory_consultations" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <div class="mb-4 flex justify-end space-x-2">
                <a href="{{ route('advisory_consultation.create', ['advisory_id' => $advisory->id, 'client_id' => $advisory->client_id]) }}"
                   class="bg-green-500 text-white px-4 py-2 rounded">
                    新規登録
                </a>
            </div>
            @if ($advisory->advisoryConsultation->isEmpty())
                <p class="text-sm text-gray-500">顧問相談は登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                    <tr>
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-5/12">件名</th>
                        <th class="border p-2 w-2/12">相談開始日</th>
                        <th class="border p-2 w-2/12">相談終了日</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                    <tbody class="text-sm">
                    @foreach ($advisory->advisoryConsultation as $advisoryConsultation)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $advisoryConsultation->id }}</td>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('advisory_consultation.show', $advisoryConsultation->id) }}" class="text-blue-500">
                                    {{ $advisoryConsultation->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $advisoryConsultation->consultation_start_date ? $advisoryConsultation->consultation_start_date : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $advisoryConsultation->consultation_end_date ? $advisoryConsultation->consultation_end_date : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $advisoryConsultation->status ? config('master.advisory_consultations_statuses')[$advisoryConsultation->status] : '&nbsp;' !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <!-- ▼ 会計一覧タブ -->
    <div id="tab-documents" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <p>会計一覧の内容（今はダミー）</p>
        </div>
    </div>


    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('advisory.update', $advisory->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">顧問契約編集</div>

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
                        <input type="text" name="title" value="{{ $advisory->title }}" class="w-full p-2 border rounded bg-white" required>
                        @errorText('title')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <select name="status" class="w-full p-2 border rounded bg-white" required>
                            <option value="">-- 選択してください --</option>
                            @foreach (config('master.advisory_contracts_statuses') as $key => $value)
                                <option value="{{ $key }}" {{ $advisory->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('status')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">説明</label>
                        <textarea name="explanation" rows="3" class="mt-1 p-2 border rounded w-full bg-white required">{{ $advisory->explanation }}</textarea>
                        @errorText('explanation')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <textarea name="special_notes" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $advisory->special_notes }}</textarea>
                        @errorText('special_notes')
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>詳細情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>

                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">契約開始日</label>
                                    <input type="date" name="advisory_start_date" value="{{ $advisory->advisory_start_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('advisory_start_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">契約終了日</label>
                                    <input type="date" name="advisory_end_date" value="{{ $advisory->advisory_end_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('advisory_end_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">顧問料月額</label>
                                    <input type="text" name="amount_monthly"
                                        value="{{ $advisory->amount_monthly }}"
                                        data-raw="{{ $advisory->amount_monthly }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('amount_monthly')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">契約期間（月）</label>
                                    <input type="number" name="contract_term_monthly" value="{{ $advisory->contract_term_monthly }}" class="w-full p-2 border rounded bg-gray-100" readonly>
                                    @errorText('contract_term_monthly')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">初回相談日</label>
                                    <input type="date" name="consultation_firstdate" value="{{ $advisory->consultation_firstdate }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('consultation_firstdate')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払区分</label>
                                    <select name="payment_category" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.payment_categories') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory->payment_category == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('payment_category')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">自動引落番号</label>
                                    <input type="text" name="adviser_fee_auto" value="{{ $advisory->adviser_fee_auto }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('adviser_fee_auto')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払方法</label>
                                    <select name="payment_method" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.payment_methods') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory->payment_method == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('payment_method')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">引落依頼額</label>
                                    <input type="text" name="withdrawal_request_amount"
                                        value="{{ $advisory->withdrawal_request_amount }}"
                                        data-raw="{{ $advisory->withdrawal_request_amount }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('withdrawal_request_amount')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">引落内訳</label>
                                    <input type="text" name="withdrawal_breakdown" value="{{ $advisory->withdrawal_breakdown }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('withdrawal_breakdown')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">引落更新日</label>
                                    <input type="date" name="withdrawal_update_date" value="{{ $advisory->withdrawal_update_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('withdrawal_update_date')
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
                                            <option value="{{ $key }}" {{ $advisory->office_id == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('office_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                                    <select name="lawyer_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory->lawyer_id }}"
                                            data-initial-text="{{ optional($advisory->lawyer)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
                                    <select name="paralegal_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory->paralegal_id }}"
                                            data-initial-text="{{ optional($advisory->paralegal)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士2</label>
                                    <select name="lawyer2_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory->lawyer2_id }}"
                                            data-initial-text="{{ optional($advisory->lawyer2)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer2_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル2</label>
                                    <select name="paralegal2_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory->paralegal2_id }}"
                                            data-initial-text="{{ optional($advisory->paralegal2)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal2_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士3</label>
                                    <select name="lawyer3_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory->lawyer3_id }}"
                                            data-initial-text="{{ optional($advisory->lawyer3)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer3_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル3</label>
                                    <select name="paralegal3_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $advisory->paralegal3_id }}"
                                            data-initial-text="{{ optional($advisory->paralegal3)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal3_id')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>ソース（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">ソース</label>
                                    <select name="source" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.routes') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory->source == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('source')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">ソース（詳細）</label>
                                    <select name="source_detail" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.routedetails') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory->source_detail == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('source_detail')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者その他</label>
                                    <input type="text" name="introducer_others" value="{{ $advisory->introducer_others }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('introducer_others')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>クライアント変更（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント</label>
                                    <select name="client_id"
                                            class="select-client-edit w-full"
                                            data-initial-id="{{ $advisory->client->id }}"
                                            data-initial-text="{{ optional($advisory->client)->name_kanji }}">
                                    </select>
                                    <option></option>
                                    @errorText('client_id')
                                </div>
                                <div></div>
                                <div class="col-span-2 mt-0 p-4 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800">
                                    新規クライアントに変更したい場合は、まず
                                    <a href="{{ route('client.create') }}" class="text-blue-600 underline font-semibold">こちらからクライアント登録</a>
                                    を行い、その後この画面で再選択してください。
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>交際情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">お中元・お歳暮</label>
                                    <select name="gift" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.gifts') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory->gift == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('gift')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">年賀状</label>
                                    <select name="newyearscard" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.newyearscards') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory->newyearscard == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('newyearscard')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('advisory.show', $advisory->id) }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">顧問契約削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの顧問契約を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('advisory.destroy', $advisory->id) }}">
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

    // ▼ 金額フィールドの初期フォーマット
    document.querySelectorAll('.currency-input').forEach(input => {
        const raw = input.dataset.raw;
        if (raw) {
            input.value = '¥' + Number(raw).toLocaleString();
        }

        input.addEventListener('input', () => {
            const value = input.value.replace(/[^\d]/g, '');
            input.value = value ? '¥' + Number(value).toLocaleString() : '';
        });
    });

    // ▼ 契約期間（月）の自動計算
    function calculateContractTerm() {
        const startInput = document.querySelector('input[name="advisory_start_date"]');
        const endInput = document.querySelector('input[name="advisory_end_date"]');
        const termInput = document.querySelector('input[name="contract_term_monthly"]');

        const start = startInput?.value ? new Date(startInput.value) : null;
        const end = endInput?.value ? new Date(endInput.value) : null;

        if (start && end && end >= start) {
            const yearDiff = end.getFullYear() - start.getFullYear();
            const monthDiff = end.getMonth() - start.getMonth();
            const totalMonths = yearDiff * 12 + monthDiff + 1;
            termInput.value = totalMonths;
        } else {
            termInput.value = '';
        }
    }

    const startInput = document.querySelector('input[name="advisory_start_date"]');
    const endInput = document.querySelector('input[name="advisory_end_date"]');

    if (startInput && endInput) {
        startInput.addEventListener('change', calculateContractTerm);
        endInput.addEventListener('change', calculateContractTerm);
    }

    // ▼ 送信前に契約期間を再計算＋金額整形
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            calculateContractTerm(); // ←ここが追加された部分
            form.querySelectorAll('.currency-input').forEach(input => {
                input.value = input.value.replace(/[^\d]/g, '');
            });
        });
    });
});
</script>
@endsection