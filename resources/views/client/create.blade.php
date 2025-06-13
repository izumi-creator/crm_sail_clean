@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">クライアント登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <!-- ヘッダー -->
    <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">
        クライアント情報
    </div>

    <!-- 入力フィールド -->
    <div class="p-6 border border-gray-300 border-t-0 text-sm">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                <ul class="list-disc pl-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('client.store') }}" method="POST">
            @csrf
            
            <!-- 個人・法人ラジオボタン -->
            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    <span class="text-red-500">*</span>個人/法人選択
                </label>
                <div class="flex space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="client_type" value="1" @checked(old('client_type') == 1) class="form-radio" onclick="toggleClientType('individual')">
                        <span class="ml-2">個人</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="client_type" value="2" @checked(old('client_type') == 2) class="form-radio" onclick="toggleClientType('corporate')">
                        <span class="ml-2">法人</span>
                    </label>
                </div>
            </div>

        <!-- 個人用項目 -->
        <div id="individual-fields" class="grid grid-cols-2 gap-6 {{ old('client_type') == 1 ? '' : 'hidden' }}">

            <!-- ▼ 見出し -->
            <div class="col-span-2 mt-6 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                基本情報
            </div>

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
                        <span class="text-red-500">*</span>クライアント名（かな） 
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
                    <label class="block font-semibold mb-1">生年月日</label>
                    <input type="date" name="individual[birthday]" value="{{ old('individual.birthday') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('individual.birthday')
                </div>
            <!-- 本人確認書類セクション -->
            <div class="col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    本人確認書類
                    <span class="text-gray-500 text-xs ml-1">(最大3件選択可)</span>
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

                <!-- 非表示の保存用フィールド -->
                <input type="hidden" name="individual[identification_document1]" id="doc1">
                <input type="hidden" name="individual[identification_document2]" id="doc2">
                <input type="hidden" name="individual[identification_document3]" id="doc3">
            </div>

            <!-- ラッパー -->
            <div class="col-span-2 mt-2 -mx-6">

                <!-- トグルボタン -->
                <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                    <span>連絡先情報（クリックで開閉）</span>
                    <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                </div>

                 <!-- アコーディオン内容 -->
                <div class="accordion-content hidden pt-4 px-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block font-semibold mb-1">携帯電話</label>
                            <input type="text" name="individual[mobile_number]" value="{{ old('individual.mobile_number') }}"
                                   placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.mobile_number')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">電話番号</label>
                            <input type="text" name="individual[phone_number]" value="{{ old('individual.phone_number') }}"
                                   placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.phone_number')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">電話番号（第一連絡先）</label>
                            <input type="text" name="individual[first_contact_number]" value="{{ old('individual.first_contact_number') }}"
                                   placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.first_contact_number')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">電話番号（第二連絡先）</label>
                            <input type="text" name="individual[second_contact_number]" value="{{ old('individual.second_contact_number') }}"
                                   placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.second_contact_number')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">メールアドレス1</label>
                            <input type="email" name="individual[email1]" value="{{ old('individual.email1') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.email1')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">メールアドレス2</label>
                            <input type="email" name="individual[email2]" value="{{ old('individual.email2') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.email2')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">自宅電話番号</label>
                            <input type="text" name="individual[home_phone_number]" value="{{ old('individual.home_phone_number') }}"
                                   placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.home_phone_number')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">FAX</label>
                            <input type="text" name="individual[fax]" value="{{ old('individual.fax') }}"
                                   placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                            @errorText('individual.fax')
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="individual[not_home_contact]" value="1"
                                       @checked(old('individual.not_home_contact') == 1)>
                                <span class="ml-2">自宅連絡不可区分</span>
                            </label>
                        </div>
                        <div></div>
                        <div>
                            <label class="block font-semibold mb-1">住所_郵便番号</label>
                            <input type="text" name="individual[address_postalcode]"
                                id="individual_address_postalcode" value="{{ old('individual.address_postalcode') }}"
                                data-zip-target="individual_address_state,individual_address_city,individual_address_street"
                                data-zip-error="zipcode-error"
                                placeholder="例: 123-4567"
                                class="w-full p-2 border rounded bg-white">
                            <p id="zipcode-error" class="text-red-600 text-sm mt-1"></p>
                            @errorText('individual.address_postalcode')
                        </div>
                         <div>
                            <label class="block font-semibold mb-1">住所（郵送先）_郵便番号</label>
                            <input type="text"name="individual[contact_postalcode]"
                               id="individual_contact_postalcode" value="{{ old('individual.contact_postalcode') }}"
                               data-zip-target="individual_contact_state,individual_contact_city,individual_contact_street"
                               data-zip-error="zipcode-error-individual-contact"
                               placeholder="例: 123-4567"
                               class="w-full p-2 border rounded bg-white">
                            <p id="zipcode-error-individual-contact" class="text-red-600 text-sm mt-1"></p>
                            @errorText('individual.contact_postalcode')
                        </div>                
                        <div>
                        <label class="block font-semibold mb-1">住所_都道府県</label>
                            <input type="text" name="individual[address_state]" id="individual_address_state" value="{{ old('individual.address_state') }}"
                                    class="w-full p-2 border rounded bg-white">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">住所（郵送先）_都道府県</label>
                            <input type="text" name="individual[contact_state]" id="individual_contact_state" value="{{ old('individual.contact_state') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.contact_state')
                        </div>                
                        <div>
                        <label class="block font-semibold mb-1">住所_市区郡</label>
                        <input type="text" name="individual[address_city]" id="individual_address_city" value="{{ old('individual.address_city') }}"
                                    class="w-full p-2 border rounded bg-white">
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">住所（郵送先）_市区郡</label>
                            <input type="text" name="individual[contact_city]" id="individual_contact_city" value="{{ old('individual.contact_city') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.contact_city')
                        </div>                
                        <div>
                        <label class="block font-semibold mb-1">住所_町名・番地</label>
                        <input type="text" name="individual[address_street]" id="individual_address_street" value="{{ old('individual.address_street') }}"
                                    class="w-full p-2 border rounded bg-white">
                        </div>                
                        <div>
                            <label class="block font-semibold mb-1">住所（郵送先）_町名・番地</label>
                            <input type="text" name="individual[contact_street]" id="individual_contact_street" value="{{ old('individual.contact_street') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.contact_street')
                        </div>                
                        <div>
                            <label class="block font-semibold mb-1">住所_宛先名（漢字）</label>
                            <input type="text" name="individual[address_name_kanji]" value="{{ old('individual.address_name_kanji') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.address_name_kanji')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">住所（郵送先）_宛先名（漢字）</label>
                            <input type="text" name="individual[contact_name_kanji]" value="{{ old('individual.contact_name_kanji') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.contact_name_kanji')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">住所_宛先名（ふりがな）</label>
                            <input type="text" name="individual[address_name_kana]" value="{{ old('individual.address_name_kana') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.address_name_kana')
                        </div>
                        <div>
                            <label class="block font-semibold mb-1">住所（郵送先）_宛先名（ふりがな）</label>
                            <input type="text" name="individual[contact_name_kana]" value="{{ old('individual.contact_name_kana') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.contact_name_kana')
                        </div>
                        <div class="col-span-2">
                            <label class="block font-semibold mb-1">連絡先特記事項</label>
                            <input type="text" name="individual[contact_address_notes]" value="{{ old('individual.contact_address_notes') }}"
                                   class="w-full p-2 border rounded bg-white">
                            @errorText('individual.contact_address_notes')
                        </div>
                    </div>
                </div>
            </div>
            <!-- ラッパー終了 -->

            <!-- ラッパー -->
            <div class="col-span-2 mt-2 -mx-6">

                <!-- トグルボタン -->
                <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                    <span>交際情報（クリックで開閉）</span>
                    <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                </div>

                <!-- アコーディオン内容 -->
                <div class="accordion-content hidden pt-4 px-6">
                <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="individual[send_newyearscard]" value="1"
                                       @checked(old('individual.send_newyearscard') == 1)>
                                <span class="ml-2">年賀状を送る</span>
                            </label>
                            @errorText('individual.send_newyearscard')
                        </div>                    
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="individual[send_summergreetingcard]" value="1"
                                       @checked(old('individual.send_summergreetingcard') == 1)>
                                <span class="ml-2">暑中見舞いを送る</span>
                            </label>
                            @errorText('individual.send_summergreetingcard')
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="individual[send_office_news]" value="1"
                                       @checked(old('individual.send_office_news') == 1)>
                                <span class="ml-2">事務所報を送る</span>
                            </label>
                            @errorText('individual.send_office_news')
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="individual[send_autocreation]" value="1"
                                       @checked(old('individual.send_autocreation') == 1)>
                                <span class="ml-2">交際情報履歴を自動作成する</span>
                            </label>
                            @errorText('individual.send_autocreation')
                        </div>
                    </div>
                </div>                       
            </div>
        </div>

        <!-- 法人用項目 -->
        <div id="corporate-fields" class="grid grid-cols-2 gap-6 {{ old('client_type') == 2 ? '' : 'hidden' }}">
            
            <!-- ▼ 見出し -->
            <div class="col-span-2 mt-6 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                基本情報
            </div>
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
                        <span class="text-red-500">*</span>クライアント名（かな） 
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

            <!-- ラッパー -->
            <div class="col-span-2 mt-2 -mx-6">

                <!-- トグルボタン -->
                <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                    <span>連絡先情報</span>
                    <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                </div>

                    <!-- アコーディオン内容 -->
                    <div class="accordion-content hidden pt-4 px-6">
                        <div class="grid grid-cols-2 gap-6">
                            <!-- 電話番号1 -->
                            <div>
                                <label class="block font-semibold mb-1">電話番号1</label>
                                <input type="text" name="corporate[phone_number]" value="{{ old('corporate.phone_number') }}"
                                       placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.phone_number')
                            </div>
                            <!-- 電話番号2 -->
                            <div>
                                <label class="block font-semibold mb-1">電話番号2</label>
                                <input type="text" name="corporate[phone_number2]" value="{{ old('corporate.phone_number2') }}"
                                       placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.phone_number2')
                            </div>
                            <!-- 電話番号（第一連絡先） -->
                            <div>
                                <label class="block font-semibold mb-1">電話番号（第一連絡先）</label>
                                <input type="text" name="corporate[first_contact_number]" value="{{ old('corporate.first_contact_number') }}"
                                       placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.first_contact_number')
                            </div>
                            <!-- 電話番号（第二連絡先）-->
                            <div>
                                <label class="block font-semibold mb-1">電話番号（第二連絡先）</label>
                                <input type="text" name="corporate[second_contact_number]" value="{{ old('corporate.second_contact_number') }}"
                                       placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.second_contact_number')
                            </div>
                            <div>
                                <label class="block font-semibold mb-1">FAX</label>
                                <input type="text" name="corporate[fax]" value="{{ old('corporate.fax') }}"
                                       placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.fax')
                            </div>
                            <div>
                                <label class="block font-semibold mb-1">メールアドレス</label>
                                <input type="email" name="corporate[email1]" value="{{ old('corporate.email1') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.email1')
                            </div>
                            <!-- 取引先責任者_電話番号 contact_phone_number -->
                            <div>
                                <label class="block font-semibold mb-1">取引先責任者_電話番号</label>
                                <input type="text" name="corporate[contact_phone_number]" value="{{ old('corporate.contact_phone_number') }}"
                                       placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_phone_number')
                            </div>
                            <!-- 取引先責任者_携帯電話 contact_mobile_number -->
                            <div>
                                <label class="block font-semibold mb-1">取引先責任者_携帯電話</label>
                                <input type="text" name="corporate[contact_mobile_number]" value="{{ old('corporate.contact_mobile_number') }}"
                                       placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_mobile_number')
                            </div>
                            <!-- 取引先責任者_自宅電話番号 contact_home_phone_number -->
                            <div>
                                <label class="block font-semibold mb-1">取引先責任者_自宅電話番号</label>
                                <input type="text" name="corporate[contact_home_phone_number]" value="{{ old('corporate.contact_home_phone_number') }}"
                                       placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_home_phone_number')
                            </div>
                            <!-- 取引先責任者_FAX contact_fax -->
                            <div>
                                <label class="block font-semibold mb-1">取引先責任者_FAX</label>
                                <input type="text" name="corporate[contact_fax]" value="{{ old('corporate.contact_fax') }}"
                                   placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_fax')
                            </div>
                            <!-- 取引先責任者_メール1 contact_email1 -->
                            <div>
                               <label class="block font-semibold mb-1">取引先責任者_メール1</label>
                               <input type="email" name="corporate[contact_email1]" value="{{ old('corporate.contact_email1') }}"
                                      class="w-full p-2 border rounded bg-white">
                               @errorText('corporate.contact_email1')
                            </div>
                            <!-- 取引先責任者_メール2 contact_email2 -->
                            <div>
                               <label class="block font-semibold mb-1">取引先責任者_メール2</label>
                               <input type="email" name="corporate[contact_email2]" value="{{ old('corporate.contact_email2') }}"
                                      class="w-full p-2 border rounded bg-white">
                               @errorText('corporate.contact_email2')
                            </div>
                            <!-- 住所_郵便番号 -->
                            <div>
                                <label class="block font-semibold mb-1">住所_郵便番号</label>
                                <input type="text" name="corporate[address_postalcode]"
                                    id="corporate_address_postalcode" value="{{ old('corporate.address_postalcode') }}"
                                    data-zip-target="corporate_address_state,corporate_address_city,corporate_address_street"
                                    data-zip-error="zipcode-error-corporate"
                                    placeholder="例: 123-4567"
                                    class="w-full p-2 border rounded bg-white">
                                <p id="zipcode-error-corporate" class="text-red-600 text-sm mt-1"></p>
                                @errorText('corporate.address_postalcode')
                            </div>
                             <!-- 住所（郵送先）_郵便番号 -->
                             <div>
                                <label class="block font-semibold mb-1">住所（郵送先）_郵便番号</label>
                                <input type="text" name="corporate[contact_postalcode]"
                                    id="corporate_contact_postalcode" value="{{ old('corporate.contact_postalcode') }}"
                                    data-zip-target="corporate_contact_state,corporate_contact_city,corporate_contact_street"
                                    data-zip-error="zipcode-error-corporate-contact"
                                    placeholder="例: 123-4567"
                                    class="w-full p-2 border rounded bg-white">
                                <p id="zipcode-error-corporate-contact" class="text-red-600 text-sm mt-1"></p>
                                @errorText('corporate.contact_postalcode')
                            </div>                
                            <!-- 住所_都道府県 -->
                            <div>
                                <label class="block font-semibold mb-1">住所_都道府県</label>
                                <input type="text" name="corporate[address_state]" id="corporate_address_state" value="{{ old('corporate.address_state') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.address_state')
                            </div>
                            <!-- 住所（郵送先）_都道府県 -->
                            <div>
                                <label class="block font-semibold mb-1">住所（郵送先）_都道府県</label>
                                <input type="text" name="corporate[contact_state]" id="corporate_contact_state" value="{{ old('corporate.contact_state') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_state')
                            </div>                
                            <!-- 住所_市区郡 -->
                            <div>
                                <label class="block font-semibold mb-1">住所_市区郡</label>
                                <input type="text" name="corporate[address_city]" id="corporate_address_city" value="{{ old('corporate.address_city') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.address_city')
                            </div>
                            <!-- 住所（郵送先）_市区郡 -->
                            <div>
                                <label class="block font-semibold mb-1">住所（郵送先）_市区郡</label>
                                <input type="text" name="corporate[contact_city]" id="corporate_contact_city" value="{{ old('corporate.contact_city') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_city')
                            </div>                
                            <!-- 住所_町名・番地 -->
                            <div>
                                <label class="block font-semibold mb-1">住所_町名・番地</label>
                                <input type="text" name="corporate[address_street]" id="corporate_address_street" value="{{ old('corporate.address_street') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.address_street')
                            </div>
                            <!-- 住所（郵送先）_町名・番地 -->
                            <div>
                                <label class="block font-semibold mb-1">住所（郵送先）_町名・番地</label>
                                <input type="text" name="corporate[contact_street]" id="corporate_contact_street" value="{{ old('corporate.contact_street') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_street')
                            </div>                
                            <!-- 住所_名（漢字） -->
                            <div>
                                <label class="block font-semibold mb-1">住所_宛先名（漢字）</label>
                                <input type="text" name="corporate[address_name_kanji]" value="{{ old('corporate.address_name_kanji') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.address_name_kanji')
                            </div>
                            <!-- 住所（郵送先）_名（漢字） -->
                            <div>
                                <label class="block font-semibold mb-1">住所（郵送先）_宛先名（漢字）</label>
                                <input type="text" name="corporate[contact_name_kanji]" value="{{ old('corporate.contact_name_kanji') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_name_kanji')
                            </div>                
                            <!-- 住所_名（かな） -->
                            <div>
                                <label class="block font-semibold mb-1">住所_宛先名（ふりがな）</label>
                                <input type="text" name="corporate[address_name_kana]" value="{{ old('corporate.address_name_kana') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.address_name_kana')
                            </div>
                            <!-- 住所（郵送先）_名（かな） -->
                            <div>
                                <label class="block font-semibold mb-1">住所（郵送先）_宛先名（ふりがな）</label>
                                <input type="text" name="corporate[contact_name_kana]" value="{{ old('corporate.contact_name_kana') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_name_kana')
                            </div>
                            <!-- 連絡先特記事項 -->
                            <div class="col-span-2">
                                <label class="block font-semibold mb-1">連絡先特記事項</label>
                                <input type="text" name="corporate[contact_address_notes]" value="{{ old('corporate.contact_address_notes') }}"
                                       class="w-full p-2 border rounded bg-white">
                                @errorText('corporate.contact_address_notes')
                            </div>
                        </div>
                    </div>
            </div>
            
            <!-- ラッパー -->
            <div class="col-span-2 mt-2 -mx-6">

                <!-- トグルボタン -->
                <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                    <span>交際情報</span>
                    <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                </div>
                <!-- アコーディオン内容 -->
                <div class="accordion-content hidden pt-4 px-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="corporate[send_newyearscard]" value="1"
                                       @checked(old('corporate.send_newyearscard') == 1)>
                                <span class="ml-2">年賀状を送る</span>
                            </label>
                            @errorText('corporate.send_newyearscard')
                        </div>                    
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="corporate[send_summergreetingcard]" value="1"
                                       @checked(old('corporate.send_summergreetingcard') == 1)>
                                <span class="ml-2">暑中見舞いを送る</span>
                            </label>
                            @errorText('corporate.send_summergreetingcard')
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="corporate[send_office_news]" value="1"
                                       @checked(old('corporate.send_office_news') == 1)>
                                <span class="ml-2">事務所報を送る</span>
                            </label>
                            @errorText('corporate.send_office_news')
                        </div>
                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="corporate[send_autocreation]" value="1"
                                       @checked(old('corporate.send_autocreation') == 1)>
                                <span class="ml-2">交際情報履歴を自動作成する</span>
                            </label>
                            @errorText('corporate.send_autocreation')
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <!-- ボタン -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('client.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                    一覧に戻る
                </a>
                <button type="button" onclick="openResetModal()" class="px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                    個人法人の選択クリア
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    登録する
                </button>
            </div>
        </form>
    </div>

    <!-- 入力クリア確認モーダル -->
    <div id="resetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-md">
            <!-- ヘッダー -->
            <div class="bg-blue-600 text-white px-4 py-2 font-bold border-b">入力クリア確認</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">すべての入力内容がクリアされますが、よろしいですか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <div class="flex justify-end space-x-2 px-6 pb-6">
                <button type="button" onclick="closeResetModal()" class="px-4 py-2 bg-gray-300 text-black rounded">
                    キャンセル
                </button>
                <button type="button" onclick="executeReset()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 min-w-[100px]">
                    クリア実行
                </button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    
        // ▼ Bladeから直接受け取る：個人 or 法人
        const clientType = @json(old('client_type'));
        console.log("client_type (old):", clientType);
    
        if (clientType === 1 || clientType === "1") {
            toggleClientType('individual');
        } else if (clientType === 2 || clientType === "2") {
            toggleClientType('corporate');
        }
    
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
    
        // ▼ アコーディオン：初期は閉じる、エラー時は開く
        if (@json($errors->any())) {
            document.querySelectorAll('.accordion-content').forEach(content => {
                content.classList.remove('hidden');
                const icon = content.previousElementSibling?.querySelector('.accordion-icon');
                icon?.classList.add('rotate-180');
            });
        }
    
        // ▼ 郵便番号 → 住所補完
        const zipInputs = document.querySelectorAll('input[data-zip-target]');
        const zipCache = {};
    
        zipInputs.forEach(input => {
            const targets = input.dataset.zipTarget.split(',');
            const errorFieldId = input.dataset.zipError;
            input.addEventListener('blur', function () {
                const rawZip = input.value.trim();
                const zipcode = rawZip.replace('-', '');
                const errorField = errorFieldId ? document.getElementById(errorFieldId) : null;
            
                if (input.offsetParent === null || input.disabled) return;
                if (zipcode === "") return errorField && (errorField.textContent = "");
            
                if (!/^\d{7}$/.test(zipcode)) {
                    if (errorField) errorField.textContent = "7桁の数字で入力してください（例: 123-4567）";
                    return;
                } else {
                    if (errorField) errorField.textContent = "";
                }
            
                if (zipCache[zipcode]) {
                    applyAddressFields(zipCache[zipcode], targets);
                    return;
                }
            
                fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${zipcode}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.results && data.results[0]) {
                            const result = data.results[0];
                            zipCache[zipcode] = result;
                            applyAddressFields(result, targets);
                        } else {
                            if (errorField) {
                                errorField.textContent = data.message || "該当する住所が見つかりませんでした。";
                            }
                        }
                    })
                    .catch(error => {
                        console.error("住所取得エラー:", error);
                        if (errorField) errorField.textContent = "通信エラーが発生しました。";
                    });
            });
        });
    
        function applyAddressFields(result, targets) {
            document.getElementById(targets[0]).value = result.address1;
            document.getElementById(targets[1]).value = result.address2;
            document.getElementById(targets[2]).value = result.address3;
        }
    
        // ▼ アコーディオン手動開閉
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
    
        // ▼ 本人確認書類：old() を反映
        const selectedValues = [
            "{{ old('individual.identification_document1') }}",
            "{{ old('individual.identification_document2') }}",
            "{{ old('individual.identification_document3') }}"
        ].filter(v => v !== '');
    
        selectedValues.forEach(val => {
            const option = document.querySelector(`#doc-available option[value="${val}"]`);
            if (option) {
                option.remove();
                document.getElementById('doc-selected').appendChild(option);
            }
        });
    
        updateHiddenFields();
    });
    
    // ▼ 個人・法人表示切替
    function toggleClientType(type) {
        document.getElementById('individual-fields').classList.toggle('hidden', type !== 'individual');
        document.getElementById('corporate-fields').classList.toggle('hidden', type !== 'corporate');
    
        document.querySelectorAll('input[name="client_type"]').forEach(radio => {
            if (!radio.checked) radio.disabled = true;
        });
    }
    
    // ▼ リセットモーダル
    function openResetModal() {
        document.getElementById('resetModal').classList.remove('hidden');
    }
    function closeResetModal() {
        document.getElementById('resetModal').classList.add('hidden');
    }
    function executeReset() {
        window.location.reload();
    }
    
    // ▼ 本人確認書類 Dual ListBox 操作
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
    
    // ▼ hidden フィールド更新
    function updateHiddenFields() {
        const selected = document.getElementById('doc-selected').options;
        document.getElementById('doc1').value = selected[0]?.value || '';
        document.getElementById('doc2').value = selected[1]?.value || '';
        document.getElementById('doc3').value = selected[2]?.value || '';
    }
    </script>

@endsection