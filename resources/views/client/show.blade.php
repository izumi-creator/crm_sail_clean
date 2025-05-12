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

    <!-- クライアント詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            <button onclick=# class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
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
                                    <div><label class="block text-sm font-semibold text-gray-700 mb-1">電話番号</label><div class="mt-1 p-2 border rounded bg-gray-50">{!! $client->phone_number ?: '&nbsp;' !!}</div></div>
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
    window.onload = function () {
        document.getElementById('editModal').classList.remove('hidden');
    };
</script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function () {
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