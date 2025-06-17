@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">相談詳細</h2>

    <!-- ✅ 上段：主要項目カード（個人／法人で出し分け） -->
    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <!-- 見出しバー -->
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-sm text-gray-100 mb-1">
                {{ $consultation->consultation_party == 1 ? '個人の相談' : '法人の相談' }}
            </div>
            <div class="text-xl font-bold">
                @if ($consultation->client)
                    <a href="{{ route('client.show', $consultation->client_id) }}" class="hover:underline">
                        {{ optional($consultation->client)->name_kanji }}（{{ optional($consultation->client)->name_kana }}）
                    </a>
                @else
                    （不明）
                @endif
            </div>
        </div>

        <!-- 内容エリア -->
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 px-6 py-4">
            @if ($consultation->consultation_party == 1)
                <!-- 個人クライアント用表示 -->
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! optional($consultation->client)->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! optional($consultation->client)->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス1:</span>
                    <span class="ml-2">{!! optional($consultation->client)->email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス2:</span>
                    <span class="ml-2">{!! optional($consultation->client)->email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($consultation->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($consultation->paralegal)->name ?: '&nbsp;' !!}</span>
                </div>
            @else
                <!-- 法人クライアント用表示 -->
                <div class="col-span-2">
                    <span class="font-semibold">取引先責任者名:</span>
                    <span class="ml-2">
                        {{ $consultation->client->contact_last_name_kanji }}　{{ $consultation->client->contact_first_name_kanji }}
                        （{{ $consultation->client->contact_last_name_kana }}　{{ $consultation->client->contact_first_name_kana }}）
                    </span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! $consultation->client->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! $consultation->client->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール1:</span>
                    <span class="ml-2">{!! $consultation->client->contact_email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール2:</span>
                    <span class="ml-2">{!! $consultation->client->contact_email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($consultation->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($consultation->paralegal)->name ?: '&nbsp;' !!}</span>
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
                関係者一覧（{{ $relatedparties->count() }}件）
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

            <!-- ✅ 相談情報の見出し＋内容を枠で囲む -->
            <div class="border border-gray-300 overflow-hidden">
                <!-- 見出し -->
                <div class="bg-sky-700 text-white px-4 py-2 font-bold border">相談情報</div>
                <!-- 内容 -->
                <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                    <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                        基本情報
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                            {!! $consultation->status ? config('master.consultation_statuses')[$consultation->status] : '&nbsp;' !!}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス詳細</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->status_detail ?: '&nbsp;' !!}</div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件概要</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $consultation->case_summary }}</pre>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $consultation->special_notes }}</pre>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>お問合せ内容</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $consultation->inquirycontent }}</pre>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $consultation->opponent_confliction ? 'checked' : '' }}>
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">第一希望日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {{ $consultation->firstchoice_datetime ? $consultation->firstchoice_datetime->format('Y-m-d H:i') : '―' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">第二希望日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {{ $consultation->secondchoice_datetime ? $consultation->secondchoice_datetime->format('Y-m-d H:i') : '―' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">問い合せ形態</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->inquirytype ? config('master.inquirytypes')[$consultation->inquirytype] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談形態</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->consultationtype ? config('master.consultation_types')[$consultation->consultationtype] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">事件分野</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->case_category ? config('master.case_categories')[$consultation->case_category] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">事件分野（詳細）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->case_subcategory ? config('master.case_subcategories')[$consultation->case_subcategory] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談受付日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->consultation_receptiondate ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">初回相談日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->consultation_firstdate ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終了日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->enddate ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談に至らなかった理由</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->consultation_notreason ? config('master.consultation_notreasons')[$consultation->consultation_notreason] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談後のフィードバック</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->consultation_feedback ? config('master.consultation_feedbacks')[$consultation->consultation_feedback] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談終了理由</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->reason_termination ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談終了理由（詳細）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->reason_termination_detail ?: '&nbsp;' !!}
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
                                        {!! $consultation->office_id ? config('master.offices_id')[$consultation->office_id] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>担当弁護士</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($consultation->lawyer)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>担当パラリーガル</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($consultation->paralegal)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士2</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($consultation->lawyer2)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル2</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($consultation->paralegal2)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士3</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($consultation->lawyer3)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル3</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($consultation->paralegal3)->name ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>見込み（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">見込理由</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->feefinish_prospect ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">報酬体系</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->feesystem ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->sales_prospect !== null ? '¥' . number_format($consultation->sales_prospect) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込更新日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->sales_reason_updated ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込（初期値）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->feesystem_initialvalue !== null ? '¥' . number_format($consultation->feesystem_initialvalue) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終了時期見込</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->enddate_prospect ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終了時期見込（初期値）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->enddate_prospect_initialvalue ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>経由情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">流入経路</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->route ? config('master.routes')[$consultation->route] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">流入経路（詳細）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $consultation->routedetail ? config('master.routedetails')[$consultation->routedetail] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->feefinish_prospect ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者その他</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->feesystem ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>関連先（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->client->name_kanji ?: '&nbsp;' !!}</div>
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">受任案件ID</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->business_id ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">顧問相談ID</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->advisory_id ?: '&nbsp;' !!}</div>
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
                    <a href="{{ route('consultation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ▼ 関係者一覧タブ -->
    <div id="tab-relatedparty" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
        <div class="mb-4 flex justify-end space-x-2">
            <a href="{{ route('relatedparty.create', ['consultation_id' => $consultation->id]) }}"
               class="bg-green-500 text-white px-4 py-2 rounded">
                新規登録
            </a>
        </div>
            @if ($relatedparties->isEmpty())
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
                        @foreach ($relatedparties as $relatedparty)
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
            <form method="POST" action="{{ route('consultation.update', $consultation->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">相談編集</div>

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
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>件名</label>
                        <input type="text" name="title" value="{{ $consultation->title }}" class="w-full p-2 border rounded bg-white" required>
                        @errorText('title')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>区分</label>
                        <select name="consultation_party" class="w-full p-2 border rounded bg-white" required>
                            <option value="">-- 選択してください --</option>
                            @foreach (config('master.consultation_parties') as $key => $value)
                                <option value="{{ $key }}" {{ $consultation->consultation_party == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('consultation_party')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <select name="status" class="w-full p-2 border rounded bg-white" required>
                            <option value="">-- 選択してください --</option>
                            @foreach (config('master.consultation_statuses') as $key => $value)
                                <option value="{{ $key }}" {{ $consultation->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('status')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス詳細</label>
                        <input type="text" name="status_detail" value="{{ $consultation->status_detail }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('status_detail')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件概要</label>
                        <textarea name="case_summary" rows="3" class="mt-1 p-2 border rounded w-full bg-white required">{{ $consultation->case_summary }}</textarea>
                        @errorText('case_summary')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <textarea name="special_notes" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $consultation->special_notes }}</textarea>
                        @errorText('special_notes')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>お問合せ内容</label>
                        <textarea name="inquirycontent" rows="3" class="mt-1 p-2 border rounded w-full bg-white required">{{ $consultation->inquirycontent }}</textarea>
                        @errorText('inquirycontent')
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="opponent_confliction" value="1"
                                {{ $consultation->opponent_confliction == 1 ? 'checked' : '' }}
                                class="form-checkbox text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">利益相反確認</span>
                        </label>
                        @errorText('opponent_confliction')
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">

                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>詳細情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>

                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <!-- 第一希望 -->
                                <div>
                                    <label class="block font-semibold mb-1">第一希望：年月日</label>
                                    <input type="date" name="firstchoice_date"
                                           value="{{ $consultation->firstchoice_datetime ? $consultation->firstchoice_datetime->format('Y-m-d') : '' }}"
                                           class="w-full p-2 border rounded bg-white">
                                    @errorText('firstchoice_date')
                                </div>
                                <div>
                                    <label class="block font-semibold mb-1">第一希望：時間</label>
                                    <select name="firstchoice_time" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 時間を選択 --</option>
                                        @for ($h = 9; $h <= 20; $h++)
                                            @foreach (['00', '15', '30', '45'] as $m)
                                                @php
                                                    $time = sprintf('%02d:%s', $h, $m);
                                                    $selected = $consultation->firstchoice_datetime && $consultation->firstchoice_datetime->format('H:i') === $time;
                                                @endphp
                                                <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>
                                                    {{ $time }}
                                                </option>
                                            @endforeach
                                        @endfor
                                    </select>
                                    @errorText('firstchoice_time')
                                </div>
                                <!-- 第二希望 -->
                                <div>
                                    <label class="block font-semibold mb-1">第二希望：年月日</label>
                                    <input type="date" name="secondchoice_date"
                                           value="{{ $consultation->secondchoice_datetime ? $consultation->secondchoice_datetime->format('Y-m-d') : '' }}"
                                           class="w-full p-2 border rounded bg-white">
                                    @errorText('secondchoice_date')
                                </div>
                                <div>
                                    <label class="block font-semibold mb-1">第二希望：時間</label>
                                    <select name="secondchoice_time" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 時間を選択 --</option>
                                        @for ($h = 9; $h <= 20; $h++)
                                            @foreach (['00', '15', '30', '45'] as $m)
                                                @php
                                                    $time = sprintf('%02d:%s', $h, $m);
                                                    $selected = $consultation->secondchoice_datetime && $consultation->secondchoice_datetime->format('H:i') === $time;
                                                @endphp
                                                <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>
                                                    {{ $time }}
                                                </option>
                                            @endforeach
                                        @endfor
                                    </select>
                                    @errorText('secondchoice_time')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>問い合せ形態</label>
                                    <select name="inquirytype" class="mt-1 p-2 border rounded w-full bg-white required">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.inquirytypes') as $key => $value)
                                            <option value="{{ $key }}" {{ $consultation->inquirytype == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('inquirytype')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>相談形態</label>
                                    <select name="consultationtype" class="mt-1 p-2 border rounded w-full bg-white required">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.consultation_types') as $key => $value)
                                            <option value="{{ $key }}" {{ $consultation->consultationtype == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('consultationtype')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">事件分野</label>
                                    <select name="case_category" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.case_categories') as $key => $value)
                                            <option value="{{ $key }}" {{ $consultation->case_category == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('case_category')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">事件分野（詳細）</label>
                                    <select name="case_subcategory" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.case_subcategories') as $key => $value)
                                            <option value="{{ $key }}" {{ $consultation->case_subcategory == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('case_subcategory')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談受付日</label>
                                    <input type="date" name="consultation_receptiondate" value="{{ $consultation->consultation_receptiondate }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('consultation_receptiondate')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">初回相談日</label>
                                    <input type="date" name="consultation_firstdate" value="{{ $consultation->consultation_firstdate }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('consultation_firstdate')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終了日</label>
                                    <input type="date" name="enddate" value="{{ $consultation->enddate }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('enddate')
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談に至らなかった理由</label>
                                    <select name="consultation_notreason" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.consultation_notreasons') as $key => $value)
                                            <option value="{{ $key }}" {{ $consultation->consultation_notreason == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('consultation_notreason')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談後のフィードバック</label>
                                    <select name="consultation_feedback" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.consultation_feedbacks') as $key => $value)
                                            <option value="{{ $key }}" {{ $consultation->consultation_feedback == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('consultation_feedback')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談終了理由</label>
                                    <input type="text" name="reason_termination" value="{{ $consultation->reason_termination }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('reason_termination')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談終了理由（詳細）</label>
                                    <input type="text" name="reason_termination_detail" value="{{ $consultation->reason_termination_detail }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('reason_termination_detail')
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
                                            <option value="{{ $key }}" {{ $consultation->office_id == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('office_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                                    <select name="lawyer_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $consultation->lawyer_id }}"
                                            data-initial-text="{{ optional($consultation->lawyer)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
                                    <select name="paralegal_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $consultation->paralegal_id }}"
                                            data-initial-text="{{ optional($consultation->paralegal)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士2</label>
                                    <select name="lawyer2_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $consultation->lawyer2_id }}"
                                            data-initial-text="{{ optional($consultation->lawyer2)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer2_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル2</label>
                                    <select name="paralegal2_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $consultation->paralegal2_id }}"
                                            data-initial-text="{{ optional($consultation->paralegal2)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal2_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士3</label>
                                    <select name="lawyer3_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $consultation->lawyer3_id }}"
                                            data-initial-text="{{ optional($consultation->lawyer3)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer3_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル3</label>
                                    <select name="paralegal3_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $consultation->paralegal3_id }}"
                                            data-initial-text="{{ optional($consultation->paralegal3)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal3_id')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">

                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>見込み（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>

                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">見込理由</label>
                                    <input type="text" name="feefinish_prospect" value="{{ $consultation->feefinish_prospect }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('feefinish_prospect')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">報酬体系</label>
                                    <input type="text" name="feesystem" value="{{ $consultation->feesystem }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('feesystem')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込</label>
                                    <input type="text" name="sales_prospect"
                                        value="{{ $consultation->sales_prospect }}"
                                        data-raw="{{ $consultation->sales_prospect }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('sales_prospect')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込更新日</label>
                                    <input type="date" name="sales_reason_updated" value="{{ $consultation->sales_reason_updated }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('sales_reason_updated')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込（初期値）</label>
                                    <input type="text" name="feesystem_initialvalue"
                                        value="{{ $consultation->feesystem_initialvalue }}"
                                        data-raw="{{ $consultation->feesystem_initialvalue }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('feesystem_initialvalue')
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終了時期見込</label>
                                    <input type="date" name="enddate_prospect" value="{{ $consultation->enddate_prospect }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('enddate_prospect')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold
                                    text-gray-700 mb-1">終了時期見込（初期値）</label>
                                    <input type="date" name="enddate_prospect_initialvalue" value="{{ $consultation->enddate_prospect_initialvalue }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('enddate_prospect_initialvalue')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>経由情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">流入経路</label>
                                    <select name="route" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.routes') as $key => $value)
                                            <option value="{{ $key }}" {{ $consultation->route == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('route')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">流入経路（詳細）</label>
                                    <select name="routedetail" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.routedetails') as $key => $value)
                                            <option value="{{ $key }}" {{ $consultation->routedetail == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('routedetail')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者</label>
                                    <input type="text" name="referrer" value="{{ $consultation->referrer }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('referrer')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者その他</label>
                                    <input type="text" name="referrer_other" value="{{ $consultation->referrer_other }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('referrer_other')
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
                                            data-initial-id="{{ $consultation->client->id }}"
                                            data-initial-text="{{ $consultation->client->name_kanji }}">
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
                            <span>関連先（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">受任案件ID</label>
                                    <input type="text" name="business_id" value="{{ $consultation->business_id }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('business_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">顧問相談ID</label>
                                    <input type="text" name="advisory_id" value="{{ $consultation->advisory_id }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('advisory_id')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('consultation.show', $consultation->id) }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">相談削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの相談を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('consultation.destroy', $consultation->id) }}">
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

    // ▽ 0. モーダル表示
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const selectedTab = btn.dataset.tab;

            // ボタンのクラス切り替え
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove(
                    'bg-white', 'text-sky-700', 'font-bold', 'border-x', 'border-t', 'border-b-0'
                );
                b.classList.add('text-gray-700');
            });

            btn.classList.add(
                'bg-white', 'text-sky-700', 'font-bold', 'border-x', 'border-t', 'border-b-0'
            );
            btn.classList.remove('text-gray-700');

            // コンテンツ切り替え
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(selectedTab)?.classList.remove('hidden');
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

    // ▼ 金額フィールドの初期フォーマット（¥ + カンマ区切り）
    document.querySelectorAll('.currency-input').forEach(input => {
        const raw = input.dataset.raw;
        if (raw) {
            input.value = '¥' + Number(raw).toLocaleString();
        }

        // ▼ 入力時にリアルタイム整形（数字以外除去 → カンマ付きに）
        input.addEventListener('input', () => {
            const value = input.value.replace(/[^\d]/g, ''); // 数字以外除去
            input.value = value ? '¥' + Number(value).toLocaleString() : '';
        });
    });

    // ▼ フォーム送信時：¥やカンマを除去して送信する
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            form.querySelectorAll('.currency-input').forEach(input => {
                input.value = input.value.replace(/[^\d]/g, '');
            });
        });
    });

});
</script>
@endsection