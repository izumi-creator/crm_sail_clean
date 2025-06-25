@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">クライアント詳細</h2>

    <!-- ✅ 上段：主要項目カード（個人／法人で出し分け） -->
    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <!-- 見出しバー -->
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-sm text-gray-100 mb-1">
                {{ $client->client_type == 1 ? '個人クライアント' : '法人クライアント' }}
            </div>
            <div class="text-xl font-bold">
                {{ $client->name_kanji }}（{{ $client->name_kana }}）
            </div>
        </div>

        <!-- 内容エリア -->
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 px-6 py-4">
            @if ($client->client_type == 1)
                <!-- 個人クライアント用表示 -->
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! $client->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! $client->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス1:</span>
                    <span class="ml-2">{!! $client->email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス2:</span>
                    <span class="ml-2">{!! $client->email2 ?: '&nbsp;' !!}</span>
                </div>
            @else
                <!-- 法人クライアント用表示 -->
                <div class="col-span-2">
                    <span class="font-semibold">取引先責任者名:</span>
                    <span class="ml-2">
                        {{ $client->contact_last_name_kanji }}　{{ $client->contact_first_name_kanji }}
                        （{{ $client->contact_last_name_kana }}　{{ $client->contact_first_name_kana }}）
                    </span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! $client->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! $client->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール1:</span>
                    <span class="ml-2">{!! $client->contact_email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール2:</span>
                    <span class="ml-2">{!! $client->contact_email2 ?: '&nbsp;' !!}</span>
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
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-consultation">
                相談一覧（{{ $client->consultations->count() }}件）
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-case">
                受任案件一覧（{{ $client->businesses->count() }}件）
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-advisory">
                顧問契約一覧（{{ $client->advisoryContracts->count() }}件）
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-advisory_consultations">
                顧問相談一覧（{{ $client->advisoryConsultations->count() }}件）
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-documents">
                会計一覧（0件）
            </button>
        </div>
    </div>


    <!-- ▼ 詳細情報タブ（今ある内容を全部この中に入れる） -->
    <div id="tab-detail" class="tab-content">

        <!-- クライアント詳細カード -->
        <div class="p-6 border rounded-lg shadow bg-white">
            <!-- 上部ボタン -->
            <div class="flex justify-end space-x-2 mb-4">
                <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
                @if (auth()->user()->role_type == 1)
                <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
                @endif
            </div>

            <!-- ✅ クライアント情報の見出し＋内容を枠で囲む -->
            <div class="border border-gray-300 overflow-hidden">
                <!-- 見出し -->
                <div class="bg-sky-700 text-white px-4 py-2 font-bold border">クライアント情報</div>
                <!-- 内容 -->
                <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                    @if ($client->client_type == 1)
                        <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                            基本情報
                        </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント名（漢字）</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->name_kanji ?: '&nbsp;' !!}</div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント名（かな）</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->name_kana ?: '&nbsp;' !!}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">姓（漢字）</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->last_name_kanji ?: '&nbsp;' !!}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">名（漢字）</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->first_name_kanji ?: '&nbsp;' !!}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">姓（かな）</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->last_name_kana ?: '&nbsp;' !!}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">名（かな）</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->first_name_kana ?: '&nbsp;' !!}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">生年月日</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->birthday ?: '&nbsp;' !!}</div>
                            </div>
                            @php
                                $docLabels = [
                                    1 => '運転免許証',
                                    2 => 'パスポート',
                                    3 => '登記情報',
                                    4 => '健康保険証',
                                    5 => '公共料金通知',
                                    6 => 'その他',
                                ];

                                $selectedDocs = collect([
                                    $client->identification_document1,
                                    $client->identification_document2,
                                    $client->identification_document3,
                                ])
                                ->filter()
                                ->map(fn($val) => $docLabels[$val] ?? '不明')
                                ->toArray();
                            @endphp

                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">本人確認書類</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! count($selectedDocs) ? implode(',', $selectedDocs) : '&nbsp;' !!}
                                </div>
                            </div>
                            <div class="col-span-2 mt-2 -mx-6">
                                <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                                    <span>連絡先情報（クリックで開閉）</span>
                                    <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                                </div>
                                <div class="accordion-content hidden pt-4 px-6">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">携帯電話</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->mobile_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">電話番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->phone_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">電話番号（第一連絡先）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->first_contact_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">電話番号（第二連絡先）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->second_contact_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス1</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->email1 ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス2</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->email2 ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">自宅電話番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->home_phone_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">FAX</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->fax ?: '&nbsp;' !!}</div></div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->not_home_contact ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">自宅連絡不可区分</span>
                                            </label>
                                        </div>
                                        <div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_郵便番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_postalcode ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_郵便番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_postalcode ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_都道府県</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_state ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_都道府県</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_state ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_市区郡</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_city ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_市区郡</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_city ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_町名・番地</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_street ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_町名・番地</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_street ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_宛先名（漢字）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_name_kanji ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_宛先名（漢字）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_name_kanji ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_宛先名（ふりがな）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_name_kana ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_宛先名（ふりがな）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_name_kana ?: '&nbsp;' !!}</div></div>
                                        <div class="col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-1">連絡先特記事項</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_address_notes ?: '&nbsp;' !!}</div></div>
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
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->send_newyearscard ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">年賀状を送る</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->send_summergreetingcard ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">暑中見舞いを送る</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->send_office_news ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">事務所報を送る</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->send_autocreation ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">交際情報履歴を自動作成する</span>
                                            </label>
                                        </div>
                                    </div>    
                                </div>
                            </div>

                    @elseif ($client->client_type == 2)
                        <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                            基本情報
                        </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント名（漢字）</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->name_kanji ?: '&nbsp;' !!}</div>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント名（かな）</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->name_kana ?: '&nbsp;' !!}</div>
                            </div>
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_姓（漢字）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_last_name_kanji ?: '&nbsp;' !!}</div></div>
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_名（漢字）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_first_name_kanji ?: '&nbsp;' !!}</div></div>
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_姓（ふりがな）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_last_name_kana ?: '&nbsp;' !!}</div></div>
                            <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_名（ふりがな）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_first_name_kana ?: '&nbsp;' !!}</div></div>

                            <div class="col-span-2 mt-2 -mx-6">
                                <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                                    <span>連絡先情報（クリックで開閉）</span>
                                    <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                                </div>
                                <div class="accordion-content hidden pt-4 px-6">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">電話番号1</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->phone_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">電話番号2</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->phone_number2 ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">電話番号（第一連絡先）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->first_contact_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">電話番号（第二連絡先）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->second_contact_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">FAX</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->fax ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->email1 ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_電話番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_phone_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_携帯電話</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_mobile_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_自宅電話番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_home_phone_number ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_FAX</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_fax ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_メール1</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_email1 ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_メール2</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_email2 ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_郵便番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_postalcode ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_郵便番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_postalcode ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_都道府県</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_state ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_都道府県</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_state ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_市区郡</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_city ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_市区郡</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_city ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_町名・番地</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_street ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_町名・番地</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_street ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_宛先名（漢字）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_name_kanji ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_宛先名（漢字）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_name_kanji ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所_宛先名（ふりがな）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->address_name_kana ?: '&nbsp;' !!}</div></div>
                                        <div><label class="block text-sm font-semibold text-gray-700 mb-1">住所（郵送先）_宛先名（ふりがな）</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_name_kana ?: '&nbsp;' !!}</div></div>
                                        <div class="col-span-2"><label class="block text-sm font-semibold text-gray-700 mb-1">連絡先特記事項</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->contact_address_notes ?: '&nbsp;' !!}</div></div>
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
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->send_newyearscard ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">年賀状を送る</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->send_summergreetingcard ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">暑中見舞いを送る</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->send_office_news ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">事務所報を送る</span>
                                            </label>
                                        </div>
                                        <div>
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $client->send_autocreation ? 'checked' : '' }}>
                                                <span class="ml-2 text-sm text-gray-700">交際情報履歴を自動作成する</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    @endif

                </div>
            </div>
            <!-- ✅ 外枠の外に表示 -->
            <div class="relative mt-6 h-10">
               <!-- 左側：一覧に戻る -->
                <div class="absolute left-0">
                    <a href="{{ route('client.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>
    <!-- ▼ 相談一覧タブ -->
    <div id="tab-consultation" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <div class="mb-4 flex justify-end space-x-2">
                <a href="{{ route('consultation.create', ['client_id' => $client->id]) }}"
                   class="bg-green-500 text-white px-4 py-2 rounded">
                    新規登録
                </a>
            </div>
            @if ($client->consultations->isEmpty())
                <p class="text-sm text-gray-500">相談は登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                    <tr>
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-5/12">件名</th>
                        <th class="border p-2 w-2/12">事件分野</th>
                        <th class="border p-2 w-2/12">事件分野（詳細）</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                    <tbody class="text-sm">
                    @foreach ($client->consultations as $consultation)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $consultation->id }}</td>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('consultation.show', $consultation->id) }}" class="text-blue-500">
                                    {{ $consultation->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $consultation->case_category ? config('master.case_categories')[$consultation->case_category] : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $consultation->case_subcategory ? config('master.case_subcategories')[$consultation->case_subcategory] : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $consultation->status ? config('master.consultation_statuses')[$consultation->status] : '&nbsp;' !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <!-- ▼ 受任案件一覧タブ -->
    <div id="tab-case" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            @if ($client->businesses->isEmpty())
                <p class="text-sm text-gray-500">受任案件は登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                    <tr>
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-5/12">件名</th>
                        <th class="border p-2 w-2/12">事件分野</th>
                        <th class="border p-2 w-2/12">事件分野（詳細）</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                    <tbody class="text-sm">
                    @foreach ($client->businesses as $business)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $business->id }}</td>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('business.show', $business->id) }}" class="text-blue-500">
                                    {{ $business->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $business->case_category ? config('master.case_categories')[$business->case_category] : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $business->case_subcategory ? config('master.case_subcategories')[$business->case_subcategory] : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $business->status ? config('master.business_statuses')[$business->status] : '&nbsp;' !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <!-- ▼ 顧問契約一覧タブ -->
    <div id="tab-advisory" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <div class="mb-4 flex justify-end space-x-2">
                <a href="{{ route('advisory.create', ['client_id' => $client->id]) }}"
                   class="bg-green-500 text-white px-4 py-2 rounded">
                    新規登録
                </a>
            </div>
            @if ($client->advisoryContracts->isEmpty())
                <p class="text-sm text-gray-500">顧問契約は登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                    <tr>
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-5/12">件名</th>
                        <th class="border p-2 w-2/12">契約開始日</th>
                        <th class="border p-2 w-2/12">契約終了日</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                    <tbody class="text-sm">
                    @foreach ($client->advisoryContracts as $advisoryContract)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $advisoryContract->id }}</td>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('advisory.show', $advisoryContract->id) }}" class="text-blue-500">
                                    {{ $advisoryContract->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $advisoryContract->advisory_start_date ? $advisoryContract->advisory_start_date : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $advisoryContract->advisory_end_date ? $advisoryContract->advisory_end_date : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $advisoryContract->status ? config('master.advisory_contracts_statuses')[$advisoryContract->status] : '&nbsp;' !!}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
    <!-- ▼ 顧問相談一覧タブ -->
    <div id="tab-advisory_consultations" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <div class="mb-4 flex justify-end space-x-2">
                <a href="{{ route('advisory_consultation.create', ['client_id' => $client->id]) }}"
                   class="bg-green-500 text-white px-4 py-2 rounded">
                    新規登録
                </a>
            </div>
            @if ($client->advisoryConsultations->isEmpty())
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
                    @foreach ($client->advisoryConsultations as $advisoryConsultation)
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
            <form method="POST" action="{{ route('client.update', $client->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">クライアント編集</div>

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
                    <!-- 個人 or 法人に応じて出し分け -->
                    @if ($client->client_type == 1)
                        <!-- ▼ 見出し -->
                        <div class="col-span-2 bg-orange-300 py-2 px-6 -mx-6">
                         基本情報
                         </div>

                        <!-- 個人: クライアント名（漢字）・姓（漢字） -->
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                <span class="text-red-500">*</span>クライアント名（漢字）</label>
                            <input type="text" name="individual[name_kanji]" id="name_kanji" value="{{ $client->name_kanji }}" readonly class="w-full p-2 border rounded bg-gray-100 text-gray-700 cursor-not-allowed">
                            @errorText('individual.name_kanji')
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                <span class="text-red-500">*</span>クライアント名（かな）</label>
                            <input type="text" name="individual[name_kana]" id="name_kana" value="{{ $client->name_kana }}" readonly class="w-full p-2 border rounded bg-gray-100 text-gray-700 cursor-not-allowed">
                            @errorText('individual.name_kana')
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                <span class="text-red-500">*</span>姓（漢字）</label>
                            <input type="text" name="individual[last_name_kanji]" id="last_name_kanji" value="{{ $client->last_name_kanji }}" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.last_name_kanji')
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                <span class="text-red-500">*</span>名（漢字）</label>
                            <input type="text" name="individual[first_name_kanji]" id="first_name_kanji" value="{{ $client->first_name_kanji }}" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.first_name_kanji')
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                <span class="text-red-500">*</span>姓（かな）</label>
                            <input type="text" name="individual[last_name_kana]" id="last_name_kana" value="{{ $client->last_name_kana }}" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.last_name_kana')
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                <span class="text-red-500">*</span>名（かな）</label>
                            <input type="text" name="individual[first_name_kana]" id="first_name_kana" value="{{ $client->first_name_kana }}" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.first_name_kana')
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                <span class="text-red-500">*</span>生年月日</label>
                            <input type="date" name="individual[birthday]" id="birthday" value="{{ $client->birthday }}" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.birthday')
                        </div>
                        <!-- 本人確認書類セクション -->
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">
                                本人確認書類 <span class="text-gray-500 text-xs ml-1">(最大3件選択可)</span>
                            </label>
                            <div class="flex space-x-4">
                                <!-- 選択可能 -->
                                <select id="doc-available" multiple size="6" class="w-1/2 border p-2 rounded">
                                    <option value="1">運転免許証</option>
                                    <option value="2">パスポート</option>
                                    <option value="3">登記情報</option>
                                    <option value="4">健康保険証</option>
                                    <option value="5">公共料金通知</option>
                                    <option value="6">その他</option>
                                </select>
                                <div class="flex flex-col justify-center space-y-2">
                                    <button type="button" onclick="moveSelected('doc-available', 'doc-selected')">&gt;</button>
                                    <button type="button" onclick="moveSelected('doc-selected', 'doc-available')">&lt;</button>
                                </div>
                                <!-- 選択済み -->
                                <select id="doc-selected" name="identification_documents[]" multiple size="6" class="w-1/2 border p-2 rounded"></select>
                            </div>
                            <!-- hidden入力で送信 -->
                            <input type="hidden" name="individual[identification_document1]" id="doc1">
                            <input type="hidden" name="individual[identification_document2]" id="doc2">
                            <input type="hidden" name="individual[identification_document3]" id="doc3">
                        </div>

                        <div class="col-span-2 mt-0 -mx-6">

                            <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                                <span>連絡先情報（クリックで開閉）</span>
                                <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                            </div>
                        
                             <!-- アコーディオン内容 -->
                            <div class="accordion-content hidden pt-4 px-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">携帯電話</label>
                                        <input type="text" name="individual[mobile_number]" id="mobile_number" value="{{ $client->mobile_number }}"
                                        placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.mobile_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号</label>
                                        <input type="text" name="individual[phone_number]" id="phone_number" value="{{ $client->phone_number }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.phone_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号（第一連絡先）</label>
                                        <input type="text" name="individual[first_contact_number]" id="first_contact_number" value="{{ $client->first_contact_number }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.first_contact_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号（第二連絡先）</label>
                                        <input type="text" name="individual[second_contact_number]" id="second_contact_number" value="{{ $client->second_contact_number }}"
                                        placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.second_contact_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス1</label>
                                        <input type="email" name="individual[email1]" id="email1" value="{{ $client->email1 }}" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.email1')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス2</label>
                                        <input type="email" name="individual[email2]" id="email2" value="{{ $client->email2 }}" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.email2')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">自宅電話番号</label>
                                        <input type="text" name="individual[home_phone_number]" id="home_phone_number" value="{{ $client->home_phone_number }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.home_phone_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">FAX</label>
                                        <input type="text" name="individual[fax]" id="fax" value="{{ $client->fax }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.fax')
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="individual[not_home_contact]" value="1"
                                                {{ $client->not_home_contact == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2">自宅連絡不可区分</span>
                                        </label>
                                        @errorText('individual.not_home_contact')
                                    </div>
                                    <div></div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_郵便番号</label>
                                        <input type="text" name="individual[address_postalcode]"
                                            id="individual_address_postalcode" value="{{ $client->address_postalcode }}"
                                            data-zip-target="individual_address_state,individual_address_city,individual_address_street"
                                            data-zip-error="zipcode-error"
                                            placeholder="例: 123-4567"
                                            class="w-full p-2 border rounded bg-white">
                                        <p id="zipcode-error" class="text-red-600 text-sm mt-1"></p>
                                        @errorText('individual.address_postalcode')
                                    </div>                                    
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_郵便番号</label>
                                        <input type="text" name="individual[contact_postalcode]"
                                            id="individual_contact_postalcode" value="{{ $client->contact_postalcode }}"
                                            data-zip-target="individual_contact_state,individual_contact_city,individual_contact_street"
                                            data-zip-error="zipcode-error-individual-contact"
                                            placeholder="例: 123-4567"
                                            class="w-full p-2 border rounded bg-white">
                                        <p id="zipcode-error-individual-contact" class="text-red-600 text-sm mt-1"></p>
                                        @errorText('individual.contact_postalcode')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_都道府県</label>
                                        <input type="text" name="individual[address_state]" id="individual_address_state" value="{{ $client->address_state }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.address_state')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_都道府県</label>
                                        <input type="text" name="individual[contact_state]" id="individual_contact_state" value="{{ $client->contact_state }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.contact_state')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_市区郡</label>
                                        <input type="text" name="individual[address_city]" id="individual_address_city" value="{{ $client->address_city }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.address_city')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_市区郡</label>
                                        <input type="text" name="individual[contact_city]" id="individual_contact_city" value="{{ $client->contact_city }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.contact_city')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_町名・番地</label>
                                        <input type="text" name="individual[address_street]" id="individual_address_street" value="{{ $client->address_street }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.address_street')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_町名・番地</label>
                                        <input type="text" name="individual[contact_street]" id="individual_contact_street" value="{{ $client->contact_street }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.contact_street')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_宛先名（漢字）</label>
                                        <input type="text" name="individual[address_name_kanji]" value="{{ $client->address_name_kanji }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.address_name_kanji')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_宛先名（漢字）</label>
                                        <input type="text" name="individual[contact_name_kanji]" value="{{ $client->contact_name_kanji }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.contact_name_kanji')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_宛先名（ふりがな）</label>
                                        <input type="text" name="individual[address_name_kana]" value="{{ $client->address_name_kana }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.address_name_kana')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_宛先名（ふりがな）</label>
                                        <input type="text" name="individual[contact_name_kana]" value="{{ $client->contact_name_kana }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.contact_name_kana')
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block font-semibold mb-1">連絡先特記事項</label>
                                        <input type="text" name="individual[contact_address_notes]" value="{{ $client->contact_address_notes }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('individual.contact_address_notes')
                                    </div>                                    
                                </div>
                            </div>
                        </div>

                        <div class="col-span-2 mt-0 -mx-6">

                            <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                                <span>交際情報（クリックで開閉）</span>
                                <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                            </div>
                        
                             <!-- アコーディオン内容 -->
                            <div class="accordion-content hidden pt-4 px-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="individual[send_newyearscard]" value="1"
                                                {{ $client->send_newyearscard == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">年賀状を送る</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="individual[send_summergreetingcard]" value="1"
                                                {{ $client->send_summergreetingcard == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">暑中見舞いを送る</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="individual[send_office_news]" value="1"
                                                {{ $client->send_office_news == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">事務所報を送る</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="individual[send_autocreation]" value="1"
                                                {{ $client->send_autocreation == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">交際情報履歴を自動作成する</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @else

                    <!-- ▼ 見出し -->
                    <div class="col-span-2 bg-orange-300 py-2 px-6 -mx-6">
                     基本情報
                     </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>クライアント名（漢字）
                        </label>
                        <input type="text" name="corporate[name_kanji]" value="{{ $client->name_kanji }}" class="w-full p-2 border rounded bg-white">
                        @errorText('corporate.name_kanji')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>クライアント名（かな）
                        </label>
                        <input type="text" name="corporate[name_kana]" value="{{ $client->name_kana }}" class="w-full p-2 border rounded bg-white">
                        @errorText('corporate.name_kana')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            取引先責任者_姓（漢字）
                        </label>
                        <input type="text" name="corporate[contact_last_name_kanji]" id="last_name_kanji" value="{{ $client->contact_last_name_kanji }}" class="w-full p-2 border rounded bg-white">
                        @errorText('corporate.contact_last_name_kanji')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            取引先責任者_名（漢字）
                        </label>
                        <input type="text" name="corporate[contact_first_name_kanji]" id="first_name_kanji" value="{{ $client->contact_first_name_kanji }}" class="w-full p-2 border rounded bg-white">
                        @errorText('corporate.contact_first_name_kanji') 
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            取引先責任者_姓（ふりがな）
                        </label>
                        <input type="text" name="corporate[contact_last_name_kana]" id="last_name_kana" value="{{ $client->contact_last_name_kana }}" class="w-full p-2 border rounded bg-white">
                        @errorText('corporate.contact_last_name_kana')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            取引先責任者_名（ふりがな）
                        </label>
                        <input type="text" name="corporate[contact_first_name_kana]" id="first_name_kana" value="{{ $client->contact_first_name_kana }}" class="w-full p-2 border rounded bg-white">
                        @errorText('corporate.contact_first_name_kana')
                    </div>

                        <div class="col-span-2 mt-0 -mx-6">

                            <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                                <span>連絡先情報（クリックで開閉）</span>
                                <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                            </div>
                        
                             <!-- アコーディオン内容 -->
                            <div class="accordion-content hidden pt-4 px-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号1</label>
                                        <input type="text" name="corporate[phone_number]" id="phone_number" value="{{ $client->phone_number }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.phone_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号2</label>
                                        <input type="text" name="corporate[phone_number2]" id="phone_number2" value="{{ $client->phone_number2 }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.phone_number2')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号（第一連絡先）</label>
                                        <input type="text" name="corporate[first_contact_number]" id="first_contact_number" value="{{ $client->first_contact_number }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.first_contact_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号（第二連絡先）</label>
                                        <input type="text" name="corporate[second_contact_number]" id="second_contact_number" value="{{ $client->second_contact_number }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.second_contact_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">FAX</label>
                                        <input type="text" name="corporate[fax]" id="fax" value="{{ $client->fax }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.fax')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス</label>
                                        <input type="email" name="corporate[email1]" id="email" value="{{ $client->email1 }}" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.email1')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_電話番号</label>
                                        <input type="text" name="corporate[contact_phone_number]" id="contact_phone_number" value="{{ $client->contact_phone_number }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_phone_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_携帯電話</label>
                                        <input type="text" name="corporate[contact_mobile_number]" id="contact_mobile_number" value="{{ $client->contact_mobile_number }}"
                                        placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_mobile_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_自宅電話番号</label>
                                        <input type="text" name="corporate[contact_home_phone_number]" id="contact_home_phone_number" value="{{ $client->contact_home_phone_number }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_home_phone_number')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_FAX</label>
                                        <input type="text" name="corporate[contact_fax]" id="contact_fax" value="{{ $client->contact_fax }}"
                                        placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_fax')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_メール1</label>
                                        <input type="email" name="corporate[contact_email1]" id="contact_email1" value="{{ $client->contact_email1 }}"
                                        class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_email1')
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-1">取引先責任者_メール2</label>
                                        <input type="email" name="corporate[contact_email2]" id="contact_email2" value="{{ $client->contact_email2 }}"
                                        class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_email2')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_郵便番号</label>
                                        <input type="text" name="corporate[address_postalcode]"
                                            id="corporate_address_postalcode" value="{{ $client->address_postalcode }}"
                                            data-zip-target="corporate_address_state,corporate_address_city,corporate_address_street"
                                            data-zip-error="zipcode-error"
                                            placeholder="例: 123-4567"
                                            class="w-full p-2 border rounded bg-white">
                                        <p id="zipcode-error" class="text-red-600 text-sm mt-1"></p>
                                        @errorText('corporate.address_postalcode')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_郵便番号</label>
                                        <input type="text" name="corporate[contact_postalcode]"
                                            id="corporate_contact_postalcode" value="{{ $client->contact_postalcode }}"
                                            data-zip-target="corporate_contact_state,corporate_contact_city,corporate_contact_street"
                                            data-zip-error="zipcode-error-corporate-contact"
                                            placeholder="例: 123-4567"
                                            class="w-full p-2 border rounded bg-white">
                                        <p id="zipcode-error-corporate-contact" class="text-red-600 text-sm mt-1"></p>
                                        @errorText('corporate.contact_postalcode')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_都道府県</label>
                                        <input type="text" name="corporate[address_state]" id="corporate_address_state" value="{{ $client->address_state }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.address_state')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_都道府県</label>
                                        <input type="text" name="corporate[contact_state]" id="corporate_contact_state" value="{{ $client->contact_state }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_state')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_市区郡</label>
                                        <input type="text" name="corporate[address_city]" id="corporate_address_city" value="{{ $client->address_city }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.address_city')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_市区郡</label>
                                        <input type="text" name="corporate[contact_city]" id="corporate_contact_city" value="{{ $client->contact_city }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_city')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_町名・番地</label>
                                        <input type="text" name="corporate[address_street]" id="corporate_address_street" value="{{ $client->address_street }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.address_street')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_町名・番地</label>
                                        <input type="text" name="corporate[contact_street]" id="corporate_contact_street" value="{{ $client->contact_street }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_street')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_宛先名（漢字）</label>
                                        <input type="text" name="corporate[address_name_kanji]" value="{{ $client->address_name_kanji }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.address_name_kanji')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_宛先名（漢字）</label>
                                        <input type="text" name="corporate[contact_name_kanji]" value="{{ $client->contact_name_kanji }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_name_kanji')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所_宛先名（ふりがな）</label>
                                        <input type="text" name="corporate[address_name_kana]" value="{{ $client->address_name_kana }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.address_name_kana')
                                    </div>
                                    <div>
                                        <label class="block font-semibold mb-1">住所（郵送先）_宛先名（ふりがな）</label>
                                        <input type="text" name="corporate[contact_name_kana]" value="{{ $client->contact_name_kana }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_name_kana')
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block font-semibold mb-1">連絡先特記事項</label>
                                        <input type="text" name="corporate[contact_address_notes]" value="{{ $client->contact_address_notes }}"
                                            class="w-full p-2 border rounded bg-white">
                                        @errorText('corporate.contact_address_notes')
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-span-2 mt-0 -mx-6">

                            <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                                <span>交際情報（クリックで開閉）</span>
                                <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                            </div>
                        
                             <!-- アコーディオン内容 -->
                            <div class="accordion-content hidden pt-4 px-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="corporate[send_newyearscard]" value="1"
                                                {{ $client->send_newyearscard == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">年賀状を送る</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="corporate[send_summergreetingcard]" value="1"
                                                {{ $client->send_summergreetingcard == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">暑中見舞いを送る</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="corporate[send_office_news]" value="1"
                                                {{ $client->send_office_news == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">事務所報を送る</span>
                                        </label>
                                    </div>
                                    <div>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="corporate[send_autocreation]" value="1"
                                                {{ $client->send_autocreation == 1 ? 'checked' : '' }}
                                                class="form-checkbox text-blue-600">
                                            <span class="ml-2 text-sm text-gray-700">交際情報履歴を自動作成する</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('client.show', $client->id) }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">クライアント削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこのクライアントを削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('client.destroy', $client->id) }}">
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

    // ▽ 2. エラーがあるアコーディオンを開く（遅延して評価）
    //setTimeout(() => {
    //    document.querySelectorAll('.accordion-content').forEach(content => {
    //        const hasError = [...content.querySelectorAll('input, select, textarea')].some(el =>
    //            el.closest('div')?.querySelector('.text-red-500')
    //        );
    //        if (hasError) {
    //            content.classList.remove('hidden'); // アコーディオン開く
    //            const icon = content.previousElementSibling?.querySelector('.accordion-icon');
    //            icon?.classList.add('rotate-180');  // ▼アイコン回転
    //        }
    //    });
    //}, 0);

    // ▽ 3. 名前補完（漢字・かな）
    const lastNameKanji = document.getElementById('last_name_kanji');
    const firstNameKanji = document.getElementById('first_name_kanji');
    const nameKanji = document.getElementById('name_kanji');
    const lastNameKana = document.getElementById('last_name_kana');
    const firstNameKana = document.getElementById('first_name_kana');
    const nameKana = document.getElementById('name_kana');

    function updateFullNameKanji() {
        nameKanji.value = `${lastNameKanji?.value || ''}　${firstNameKanji?.value || ''}`.trim();
    }

    function updateFullNameKana() {
        nameKana.value = `${lastNameKana?.value || ''}　${firstNameKana?.value || ''}`.trim();
    }

    lastNameKanji?.addEventListener('input', updateFullNameKanji);
    firstNameKanji?.addEventListener('input', updateFullNameKanji);
    lastNameKana?.addEventListener('input', updateFullNameKana);
    firstNameKana?.addEventListener('input', updateFullNameKana);

    // ▽ 4. 本人確認書類：初期復元と移動ロジック
    const selectedValues = [
        '{{ $client->identification_document1 }}',
        '{{ $client->identification_document2 }}',
        '{{ $client->identification_document3 }}'
    ].filter(v => v);

    selectedValues.forEach(val => {
        const option = document.querySelector(`#doc-available option[value='${val}']`);
        if (option) {
            option.remove();
            document.getElementById('doc-selected')?.appendChild(option);
        }
    });
    updateHiddenFields();

    function moveSelected(fromId, toId) {
        const from = document.getElementById(fromId);
        const to = document.getElementById(toId);
        [...from.selectedOptions].forEach(option => {
            if (to.options.length >= 3 && toId === 'doc-selected') {
                alert("最大3件まで選択可能です");
                return;
            }
            to.appendChild(option);
        });
        updateHiddenFields();
    }

    window.moveSelected = moveSelected; // グローバル化

    function updateHiddenFields() {
        const selected = document.getElementById('doc-selected')?.options;
        document.getElementById('doc1').value = selected[0]?.value || '';
        document.getElementById('doc2').value = selected[1]?.value || '';
        document.getElementById('doc3').value = selected[2]?.value || '';
    }

    // ▽ 5. 郵便番号API連携
    const zipInputs = document.querySelectorAll('input[data-zip-target]');
    const zipCache = {};

    zipInputs.forEach(input => {
        const targets = input.dataset.zipTarget.split(',');
        const errorFieldId = input.dataset.zipError;
        const errorField = errorFieldId ? document.getElementById(errorFieldId) : null;

        input.addEventListener('blur', function () {
            const rawZip = input.value.trim();
            const zipcode = rawZip.replace('-', '');

            if (input.offsetParent === null || input.disabled) return;
            if (!/^\d{7}$/.test(zipcode)) {
                if (errorField) errorField.textContent = "7桁の数字で入力してください（例: 1234567）";
                return;
            } else if (errorField) {
                errorField.textContent = "";
            }

            if (zipCache[zipcode]) {
                applyZipResult(zipCache[zipcode], targets);
                return;
            }

            fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zipcode}`)
                .then(res => res.json())
                .then(data => {
                    if (data.results && data.results[0]) {
                        const result = data.results[0];
                        zipCache[zipcode] = result;
                        applyZipResult(result, targets);
                    } else {
                        if (errorField) {
                            errorField.textContent = data.message || "該当する住所が見つかりませんでした。";
                        }
                    }
                })
                .catch(() => {
                    if (errorField) errorField.textContent = "住所取得エラーが発生しました。";
                });
        });
    });

    function applyZipResult(result, targets) {
        document.getElementById(targets[0]).value = result.address1;
        document.getElementById(targets[1]).value = result.address2;
        document.getElementById(targets[2]).value = result.address3;
    }

});
</script>
@endsection