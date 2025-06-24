@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">受任案件詳細</h2>

    <!-- ✅ 上段：主要項目カード（個人／法人で出し分け） -->
    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <!-- 見出しバー -->
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-sm text-gray-100 mb-1">
                {{ $business->consultation_party == 1 ? '個人の相談' : '法人の相談' }}
            </div>
            <div class="text-xl font-bold">
                @if ($business->client)
                    <a href="{{ route('client.show', $business->client_id) }}" class="hover:underline">
                        {{ optional($business->client)->name_kanji }}（{{ optional($business->client)->name_kana }}）
                    </a>
                @else
                    （不明）
                @endif
            </div>
        </div>

        <!-- 内容エリア -->
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 px-6 py-4">
            @if ($business->consultation_party == 1)
                <!-- 個人クライアント用表示 -->
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! optional($business->client)->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! optional($business->client)->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス1:</span>
                    <span class="ml-2">{!! optional($business->client)->email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス2:</span>
                    <span class="ml-2">{!! optional($business->client)->email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($business->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($business->paralegal)->name ?: '&nbsp;' !!}</span>
                </div>
            @else
                <!-- 法人クライアント用表示 -->
                <div class="col-span-2">
                    <span class="font-semibold">取引先責任者名:</span>
                    <span class="ml-2">
                        {{ $business->client->contact_last_name_kanji }}　{{ $business->client->contact_first_name_kanji }}
                        （{{ $business->client->contact_last_name_kana }}　{{ $business->client->contact_first_name_kana }}）
                    </span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! $business->client->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! $business->client->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール1:</span>
                    <span class="ml-2">{!! $business->client->contact_email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール2:</span>
                    <span class="ml-2">{!! $business->client->contact_email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($business->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($business->paralegal)->name ?: '&nbsp;' !!}</span>
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

            <!-- ✅ 受任案件情報の見出し＋内容を枠で囲む -->
            <div class="border border-gray-300 overflow-hidden">
                <!-- 見出し -->
                <div class="bg-sky-700 text-white px-4 py-2 font-bold border">受任案件情報</div>
                <!-- 内容 -->
                <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                    <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                        基本情報
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                            {!! $business->status ? config('master.business_statuses')[$business->status] : '&nbsp;' !!}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス詳細</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->status_detail ?: '&nbsp;' !!}</div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件概要</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $business->case_summary }}</pre>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $business->special_notes }}</pre>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>詳細情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>問い合せ形態</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->inquirytype ? config('master.inquirytypes')[$business->inquirytype] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>相談形態</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->consultationtype ? config('master.consultation_types')[$business->consultationtype] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件分野</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->case_category ? config('master.case_categories')[$business->case_category] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件分野（詳細）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->case_subcategory ? config('master.case_subcategories')[$business->case_subcategory] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">受任日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->appointment_date ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終結日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->close_date ?: '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">クローズ理由</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->close_reason ? config('master.close_reasons')[$business->close_reason] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">時効完成日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->status_limitday ?: '&nbsp;' !!}
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>取扱事務所</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->office_id ? config('master.offices_id')[$business->office_id] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>担当弁護士</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($business->lawyer)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>担当パラリーガル</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($business->paralegal)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士2</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($business->lawyer2)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル2</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($business->paralegal2)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士3</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($business->lawyer3)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル3</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($business->paralegal3)->name ?: '&nbsp;' !!}</div>
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
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->feefinish_prospect ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">報酬体系</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->feesystem ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->sales_prospect !== null ? '¥' . number_format($business->sales_prospect) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込更新日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->sales_reason_updated ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">売上見込（初期値）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->feesystem_initialvalue !== null ? '¥' . number_format($business->feesystem_initialvalue) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終了時期見込</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->enddate_prospect ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終了時期見込（初期値）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->enddate_prospect_initialvalue ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $business->delay_check ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">ディレイチェック</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>弁護士費用（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">着手金</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->deposit !== null ? '¥' . number_format($business->deposit) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">成果報酬</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->performance_reward !== null ? '¥' . number_format($business->performance_reward) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">差額</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->difference !== null ? '¥' . number_format($business->difference) : '&nbsp;' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>預り依頼金（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">預り依頼金（予定）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->requestfee_initialvalue !== null ? '¥' . number_format($business->requestfee_initialvalue) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">預り依頼金</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->requestfee !== null ? '¥' . number_format($business->requestfee) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">預り金残</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->requestfee_balance !== null ? '¥' . number_format($business->requestfee_balance) : '&nbsp;' !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>養育費管理（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $business->childsupport_collect ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">養育費回収フラグ</span>
                                    </label>
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">フェーズ</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->childsupport_phase ? config('master.childsupport_phases')[$business->childsupport_phase] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_payment_date ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">養育費月額</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->childsupport_monthly_fee !== null ? '¥' . number_format($business->childsupport_monthly_fee) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払期間（開始）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_start_payment ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">養育費月額報酬</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->childsupport_monthly_remuneration !== null ? '¥' . number_format($business->childsupport_monthly_remuneration) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払期間（終了）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_end_payment ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">未回収金額</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->childsupport_notcollected_amount !== null ? '¥' . number_format($business->childsupport_notcollected_amount) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">依頼者送金額</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->childsupport_remittance_amount !== null ? '¥' . number_format($business->childsupport_remittance_amount) : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">入金先口座</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_deposit_taccount ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">返金日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_repayment_date ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">入金日</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_deposit_date ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">返金先口座の金融機関名</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_financialinstitution_name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">振込元口座名義</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_transfersource_name ?: '&nbsp;' !!}</div>
                                </div>
                                                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">返金先口座名義</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_refundaccount_name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $business->childsupport_temporary_payment ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">臨時払いの有無</span>
                                    </label>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">備考</label>
                                    <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $business->childsupport_memo }}</pre>
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
                                        {!! $business->route ? config('master.routes')[$business->route] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">流入経路（詳細）</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->routedetail ? config('master.routedetails')[$business->routedetail] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->introducer ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者その他</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->introducer_others ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>コメント（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">コメント</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->comment ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">進捗コメント</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->progress_comment ?: '&nbsp;' !!}</div>
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
                                    <label class="font-bold">相談：件名</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        @if ($business->consultation)
                                            {{ $business->consultation->title }}
                                        @elseif ($business->consultation_id)
                                            <span class="text-gray-400">（削除された相談）</span>
                                        @else
                                            {{-- 空白（何も表示しない） --}}
                                            &nbsp;
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">顧問相談ID</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->advisory_id ?: '&nbsp;' !!}</div>
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
                    <a href="{{ route('business.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ▼ 関係者一覧タブ -->
    <div id="tab-relatedparty" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
        <div class="mb-4 flex justify-end space-x-2">
            <a href="{{ route('relatedparty.create', ['business_id' => $business->id]) }}"
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

    <!-- 削除モーダル -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-md">
            <!-- ヘッダー -->
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">受任案件削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの受任案件を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('business.destroy', $business->id) }}">
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

            // 自動計算を実行
            updateCalculatedFields();
        });
    });

    // ▼ 自動計算関数（差額、預り金残）
    function updateCalculatedFields() {
        const parseValue = (selector) => {
            const el = document.querySelector(selector);
            return el ? parseInt((el.value || '').replace(/[^\d]/g, ''), 10) || 0 : 0;
        };

        // ✅ 差額 = 売上見込 - 着手金 - 成果報酬
        const sales = parseValue('input[name="sales_prospect"]');
        const deposit = parseValue('input[name="deposit"]');
        const reward = parseValue('input[name="performance_reward"]');
        const difference = sales - deposit - reward;
        const differenceInput = document.querySelector('input[name="difference"]');
        if (differenceInput) {
            differenceInput.value = '¥' + difference.toLocaleString();
        }

        // 預り金残 = 預り依頼金（予定） - 預り依頼金
        const initial = parseValue('input[name="requestfee_initialvalue"]');
        const current = parseValue('input[name="requestfee"]');
        const balance = initial - current;
        const balanceInput = document.querySelector('input[name="requestfee_balance"]');
        if (balanceInput) {
            balanceInput.value = '¥' + balance.toLocaleString();
        }
    }

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