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
                {{ $business->consultation_party == 1 ? '個人の受任案件' : '法人の受任案件' }}
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
                <div>
                    <span class="font-semibold">ステータス:</span>
                    <span class="ml-2">{!! $business->status ? config('master.business_statuses')[$business->status] : '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">Googleフォルダ:</span>
                    @if (!empty($business->folder_id))
                        <span class="ml-2">
                            <a href="https://drive.google.com/drive/folders/{{ $business->folder_id }}" class="text-blue-600 underline" target="_blank" rel="noopener">
                                フォルダを開く
                            </a>
                        </span>
                    @else
                        <span class="ml-2">（登録なし）</span>
                    @endif
                </div>
            @else
                <!-- 法人クライアント用表示 -->
                <div class="col-span-2">
                    <span class="font-semibold">取引先責任者名:</span>
                    <span class="ml-2">
                        {{ optional($business->client)->contact_last_name_kanji }}　{{ optional($business->client)->contact_first_name_kanji }}
                        （{{ optional($business->client)->contact_last_name_kana }}　{{ optional($business->client)->contact_first_name_kana }}）
                    </span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! optional($business->client)->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! optional($business->client)->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール1:</span>
                    <span class="ml-2">{!! optional($business->client)->contact_email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール2:</span>
                    <span class="ml-2">{!! optional($business->client)->contact_email2 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当弁護士:</span>
                    <span class="ml-2">{!! optional($business->lawyer)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">担当パラリーガル:</span>
                    <span class="ml-2">{!! optional($business->paralegal)->name ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">ステータス:</span>
                    <span class="ml-2">{!! $business->status ? config('master.business_statuses')[$business->status] : '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">Googleフォルダ:</span>
                    @if (!empty($business->folder_id))
                        <span class="ml-2">
                            <a href="https://drive.google.com/drive/folders/{{ $business->folder_id }}" class="text-blue-600 underline" target="_blank" rel="noopener">
                                フォルダを開く
                            </a>
                        </span>
                    @else
                        <span class="ml-2">（登録なし）</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- ✅ タスク・折衝履歴（非対称な2列構成） -->
    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <!-- 見出しバー -->
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">タスク・折衝履歴</div>
        </div>

        <!-- グリッド：左がタスク、右が折衝履歴 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-6 py-4 text-sm text-gray-700">

            {{-- 📋 タスク一覧 --}}
            <div>
                <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border flex items-center justify-between">
                    <div>📋 タスク一覧（{{ $business->tasks->count() }}件）</div>
                    <a href="{{ route('task.create', [
                        'related_party' => 2,
                        'business_id' => $business->id,
                        'redirect_url' => route('business.show', ['business' => $business->id])
                    ]) }}"
                    class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded">
                        追加
                    </a>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-h-64 overflow-y-auto pr-2">
                    @foreach ($business->tasks
                        ->sortBy('deadline_date')
                        ->sortBy('status') as $task)
                        <div class="border rounded shadow-sm p-3 bg-white text-sm leading-tight">
                            <div class="font-bold text-sky-700 mb-1">{{ $task->title }}</div>
                            <div><span class="font-semibold">大区分:</span> {{ config('master.records_1')[$task->record1] ?? '―' }}</div>
                            <div><span class="font-semibold">担当:</span> {{ optional($task->worker)->name }}</div>
                            <div><span class="font-semibold">期限:</span> {{ $task->deadline_date }}</div>
                            <div><span class="font-semibold">ステータス:</span> {{ config('master.task_statuses')[$task->status] ?? '―' }}</div>
                            <div class="mt-2">
                                <a href="{{ route('task.show', $task->id) }}" class="text-blue-600 hover:underline text-sm">詳細を見る</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 📝 折衝履歴 --}}
            <div>
                <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border flex items-center justify-between">
                    <div>📋 折衝履歴（{{ $business->negotiations->count() }}件）</div>
                    <a href="{{ route('negotiation.create', [
                        'related_party' => 2,
                        'business_id' => $business->id,
                        'redirect_url' => route('business.show', ['business' => $business->id])
                    ]) }}"
                    class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1.5 rounded">
                        追加
                    </a>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-h-64 overflow-y-auto pr-2">
                    @foreach ($business->negotiations
                        ->sortBy('status') as $negotiation)
                        <div class="border rounded shadow-sm p-3 bg-white text-sm leading-tight">
                            <div class="font-bold text-sky-700 mb-1">{{ $negotiation->title }}</div>
                            <div><span class="font-semibold">大区分:</span> {{ config('master.records_1')[$negotiation->record1] ?? '―' }}</div>
                            <div><span class="font-semibold">担当:</span> {{ optional($negotiation->worker)->name }}</div>
                            <div><span class="font-semibold">登録日:</span> {{ $negotiation->record_date }}</div>
                            <div><span class="font-semibold">ステータス:</span> {{ config('master.task_statuses')[$negotiation->status] ?? '―' }}</div>
                            <div class="mt-2">
                                <a href="{{ route('negotiation.show', $negotiation->id) }}" class="text-blue-600 hover:underline text-sm">詳細を見る</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <!-- タブ切替ボタン -->
    <div class="mb-0 overflow-x-auto border-b border-gray-300 bg-gray-100 rounded-t">
        <div class="flex space-x-2 pt-2 px-6 w-fit">
            <button class="tab-btn active-tab px-4 py-2 text-sm font-bold text-sky-700 bg-white border-x border-t border-b-0 rounded-t" data-tab="tab-detail">
                詳細情報
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-relatedparty">
                関係者一覧（{{ $business->relatedParties->count() }}件）
            </button>
            {{-- <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-task">
                タスク一覧（{{ $business->tasks->count() }}件）
            </button> --}}
            {{-- <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-negotiations">
                折衝履歴一覧（{{ $business->negotiations->count() }}件）
            </button> --}}
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-courtTask">
                裁判所対応（{{ $business->courtTasks->count() }}件）
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-documents">
                会計一覧（ダミー）
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                            {!! $business->status ? config('master.business_statuses')[$business->status] : '&nbsp;' !!}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス詳細</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->status_detail ?: '&nbsp;' !!}</div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">事件概要</label>
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">問い合せ形態</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->inquirytype ? config('master.inquirytypes')[$business->inquirytype] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談形態</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->consultationtype ? config('master.consultation_types')[$business->consultationtype] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">事件分野</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->case_category ? config('master.case_categories')[$business->case_category] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">事件分野（詳細）</label>
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
                                        {!! $business->close_notreason ? config('master.close_notreasons')[$business->close_notreason] : '&nbsp;' !!}
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
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">取扱事務所</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $business->office_id ? config('master.offices_id')[$business->office_id] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">GoogleDriveフォルダID</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->folder_id ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($business->lawyer)->name ?: '&nbsp;' !!}</div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
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
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $business->childsupport_deposit_account ?: '&nbsp;' !!}</div>
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
                            <span>期日メモ（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">期日メモ</label>
                                    <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $business->duedate_memo }}</pre>
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談：件名</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        @if ($business->consultation)
                                            <a href="{{ route('consultation.show', $business->consultation->id) }}"
                                               class="text-blue-600 underline hover:text-blue-800">
                                                {{ $business->consultation->title }}
                                            </a>
                                        @elseif ($business->consultation_id)
                                            <span class="text-gray-400">（削除された相談）</span>
                                        @else
                                            {{-- 空白（何も表示しない） --}}
                                            &nbsp;
                                        @endif
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
                    <a href="{{ route('business.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ▼ 関係者一覧タブ -->
    <div id="tab-relatedparty" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <div class="mb-4 flex justify-end space-x-2">
                <a href="{{ route('relatedparty.create', [
                    'business_id' => $business->id,
                    'redirect_url' => route('business.show', ['business' => $business->id]) . '#tab-relatedparty'
                ]) }}"
                class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>                
            </div>
            @if ($business->relatedparties->isEmpty())
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
                        @foreach ($business->relatedparties as $relatedparty)
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

    <!-- ▼ タスク一覧タブ -->
    <div id="tab-task" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
        <div class="mb-4 flex justify-end space-x-2">
            <a href="{{ route('task.create', [
                'related_party' => 2,
                'business_id' => $business->id,
                'redirect_url' => route('business.show', ['business' => $business->id]) . '#tab-task'
            ]) }}"
            class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
        </div>
            @if ($business->tasks->isEmpty())
                <p class="text-sm text-gray-500">タスクは登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                        <tr>
                            <th class="border p-2 w-1/12">ID</th>
                            <th class="border p-2 w-5/12">件名</th>
                            <th class="border p-2 w-2/12">大区分</th>
                            <th class="border p-2 w-2/12">worker名</th>
                            <th class="border p-2 w-2/12">期限日</th>
                            <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($business->tasks as $task)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $task->id }}</td>
                            <td class="border px-2 py-[6px] truncate">
                            <a href="{{ route('task.show', $task->id) }}" class="text-blue-500">
                                {{ $task->title }}
                            </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.records_1')[$task->record1] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! optional($task->worker)->name ?: '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">{{ $task->deadline_date }}</td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.task_statuses')[$task->status] ?? '未設定' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- ▼ 折衝履歴タブ -->
    <div id="tab-negotiations" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
        <div class="mb-4 flex justify-end space-x-2">
            <a href="{{ route('negotiation.create', [
                'related_party' => 2,
                'business_id' => $business->id,
                'redirect_url' => route('business.show', ['business' => $business->id]) . '#tab-negotiations'
            ]) }}"
            class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
        </div>
            @if ($business->negotiations->isEmpty())
                <p class="text-sm text-gray-500">折衝履歴は登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                        <tr>
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-5/12">件名</th>
                        <th class="border p-2 w-2/12">大区分</th>
                        <th class="border p-2 w-2/12">worker名</th>
                        <th class="border p-2 w-2/12">登録日</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($business->negotiations as $negotiation)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $negotiation->id }}</td>
                            <td class="border px-2 py-[6px] truncate">
                            <a href="{{ route('negotiation.show', $negotiation->id) }}" class="text-blue-500">
                                {{ $negotiation->title }}
                            </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.records_1')[$negotiation->record1] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! optional($negotiation->worker)->name ?: '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">{{ $negotiation->record_date }}</td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.task_statuses')[$negotiation->status] ?? '未設定' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- ▼ 裁判所対応タブ -->
    <div id="tab-courtTask" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">

            <div class="mb-4 flex justify-end space-x-2">
                <a href="{{ route('court_task.create', ['business' => $business->id]) }}"
                   class="bg-green-500 text-white px-4 py-2 rounded">
                    新規登録
                </a>
            </div>

            @if ($business->courtTasks->isEmpty())
                <p class="text-sm text-gray-500">裁判所対応は登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                        <tr>
                            <th class="border p-2 w-[6%]">ID</th>
                            <th class="border p-2 w-[28%]">タスク名</th>
                            <th class="border p-2 w-[12%]">タスク分類</th>
                            <th class="border p-2 w-[14%]">担当弁護士</th>
                            <th class="border p-2 w-[14%]">担当パラリーガル</th>
                            <th class="border p-2 w-[14%]">期限</th>
                            <th class="border p-2 w-[12%]">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($business->courtTasks as $courtTask)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $courtTask->id }}</td>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('court_task.show', $courtTask->id) }}" class="text-blue-500">
                                    {{ $courtTask->task_title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.court_task_categories')[(string)$courtTask->task_category] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! optional($courtTask->lawyer)->name ?: '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! optional($courtTask->paralegal)->name ?: '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $courtTask->deadline ? $courtTask->deadline->format('Y-m-d H:i') : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.court_tasks_statuses')[(string)$courtTask->status] ?? '未設定' }}
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
            <form method="POST" action="{{ route('business.update', $business->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">受任案件編集</div>

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
                        <input type="text" name="title" value="{{ $business->title }}" class="w-full p-2 border rounded bg-white" required>
                        @errorText('title')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <select name="status" class="w-full p-2 border rounded bg-white" required>
                            <option value="">-- 選択してください --</option>
                            @foreach (config('master.business_statuses') as $key => $value)
                                <option value="{{ $key }}" {{ $business->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('status')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス詳細</label>
                        <input type="text" name="status_detail" value="{{ $business->status_detail }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('status_detail')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件概要</label>
                        <textarea name="case_summary" rows="3" class="mt-1 p-2 border rounded w-full bg-white required">{{ $business->case_summary }}</textarea>
                        @errorText('case_summary')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <textarea name="special_notes" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $business->special_notes }}</textarea>
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>問い合せ形態</label>
                                    <select name="inquirytype" class="mt-1 p-2 border rounded w-full bg-white required">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.inquirytypes') as $key => $value)
                                            <option value="{{ $key }}" {{ $business->inquirytype == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('inquirytype')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談形態</label>
                                    <select name="consultationtype" class="mt-1 p-2 border rounded w-full bg-white required">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.consultation_types') as $key => $value)
                                            <option value="{{ $key }}" {{ $business->consultationtype == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('consultationtype')
                                </div>
                                <!-- 親：事件分野 -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件分野</label>
                                    <select id="case_category" name="case_category" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 未選択 --</option>
                                        @foreach (config('master.case_categories') as $key => $label)
                                            <option value="{{ $key }}" @selected($business->case_category == $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('case_category')
                                </div>
                            
                                <!-- 子：事件分野（詳細） -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件分野（詳細）</label>
                                    <select id="case_subcategory" name="case_subcategory" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 未選択 --</option>
                                        {{-- JSで上書き --}}
                                    </select>
                                    @errorText('case_subcategory')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>受任日</label>
                                    <input type="date" name="appointment_date" value="{{ $business->appointment_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('appointment_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">終結日</label>
                                    <input type="date" name="close_date" value="{{ $business->close_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('close_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">クローズ理由</label>
                                    <select name="close_notreason" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.close_notreasons') as $key => $value)
                                            <option value="{{ $key }}" {{ $business->close_notreason == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('close_notreason')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">時効完成日</label>
                                    <input type="date" name="status_limitday" value="{{ $business->status_limitday }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('status_limitday')
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
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>取扱事務所</label>
                                    <select name="office_id" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.offices_id') as $key => $value)
                                            <option value="{{ $key }}" {{ $business->office_id == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('office_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">GoogleDriveフォルダID</label>
                                    <input type="text" name="folder_id" 
                                    placeholder="例：1A2B3C4D5E6F7G8H9I0J"
                                    value="{{ $business->folder_id }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('folder_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>担当弁護士</label>
                                    <select name="lawyer_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $business->lawyer_id }}"
                                            data-initial-text="{{ optional($business->lawyer)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>担当パラリーガル</label>
                                    <select name="paralegal_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $business->paralegal_id }}"
                                            data-initial-text="{{ optional($business->paralegal)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士2</label>
                                    <select name="lawyer2_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $business->lawyer2_id }}"
                                            data-initial-text="{{ optional($business->lawyer2)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer2_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル2</label>
                                    <select name="paralegal2_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $business->paralegal2_id }}"
                                            data-initial-text="{{ optional($business->paralegal2)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('paralegal2_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士3</label>
                                    <select name="lawyer3_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $business->lawyer3_id }}"
                                            data-initial-text="{{ optional($business->lawyer3)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('lawyer3_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル3</label>
                                    <select name="paralegal3_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $business->paralegal3_id }}"
                                            data-initial-text="{{ optional($business->paralegal3)->name }}">
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>見込理由</label>
                                    <input type="text" name="feefinish_prospect" value="{{ $business->feefinish_prospect }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('feefinish_prospect')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>報酬体系</label>
                                    <input type="text" name="feesystem" value="{{ $business->feesystem }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('feesystem')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>売上見込</label>
                                    <input type="text" name="sales_prospect"
                                        value="{{ $business->sales_prospect }}"
                                        data-raw="{{ $business->sales_prospect }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('sales_prospect')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>売上見込更新日</label>
                                    <input type="date" name="sales_reason_updated" value="{{ $business->sales_reason_updated }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('sales_reason_updated')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>売上見込（初期値）</label>
                                    <input type="text" name="feesystem_initialvalue"
                                        value="{{ $business->feesystem_initialvalue }}"
                                        data-raw="{{ $business->feesystem_initialvalue }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('feesystem_initialvalue')
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>終了時期見込</label>
                                    <input type="date" name="enddate_prospect" value="{{ $business->enddate_prospect }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('enddate_prospect')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold
                                    text-gray-700 mb-1"><span class="text-red-500">*</span>終了時期見込（初期値）</label>
                                    <input type="date" name="enddate_prospect_initialvalue" value="{{ $business->enddate_prospect_initialvalue }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('enddate_prospect_initialvalue')
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="delay_check" value="0">
                                        <input type="checkbox" name="delay_check" value="1"
                                            {{ $business->delay_check == 1 ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">ディレイチェック</span>
                                    </label>
                                    @errorText('delay_check')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>弁護士費用（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>

                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">着手金</label>
                                    <input type="text" name="deposit"
                                        value="{{ $business->deposit }}"
                                        data-raw="{{ $business->deposit }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('deposit')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">成果報酬</label>
                                    <input type="text" name="performance_reward"
                                        value="{{ $business->performance_reward }}"
                                        data-raw="{{ $business->performance_reward }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('performance_reward')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">差額</label>
                                    <input type="text" name="difference"
                                        placeholder="自動計算：売上見込 - 着手金 - 成果報酬"
                                        value="{{ $business->difference }}"
                                        data-raw="{{ $business->difference }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-gray-100 cursor-not-allowed"
                                        readonly>
                                    @errorText('difference')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>預り依頼金（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>

                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">預り依頼金（予定）</label>
                                    <input type="text" name="requestfee_initialvalue"
                                        value="{{ $business->requestfee_initialvalue }}"
                                        data-raw="{{ $business->requestfee_initialvalue }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('requestfee_initialvalue')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">預り依頼金</label>
                                    <input type="text" name="requestfee"
                                        value="{{ $business->requestfee }}"
                                        data-raw="{{ $business->requestfee }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('requestfee')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">預り金残</label>
                                    <input type="text" name="requestfee_balance"
                                        placeholder="自動計算：預り依頼金（予定） - 預り依頼金"
                                        value="{{ $business->requestfee_balance }}"
                                        data-raw="{{ $business->requestfee_balance }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-gray-100 cursor-not-allowed"
                                        readonly>
                                    @errorText('requestfee_balance')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>養育費管理（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>

                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="childsupport_collect" value="0">
                                        <input type="checkbox" name="childsupport_collect" value="1"
                                            {{ $business->childsupport_collect == 1 ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">養育費回収フラグ</span>
                                    </label>
                                    @errorText('childsupport_collect')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">フェーズ</label>
                                    <select name="childsupport_phase" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.childsupport_phases') as $key => $value)
                                            <option value="{{ $key }}" {{ $business->childsupport_phase == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('childsupport_phase')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払日</label>
                                    <input type="date" name="childsupport_payment_date" value="{{ $business->childsupport_payment_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_payment_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">養育費月額</label>
                                    <input type="text" name="childsupport_monthly_fee"
                                        value="{{ $business->childsupport_monthly_fee }}"
                                        data-raw="{{ $business->childsupport_monthly_fee }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_monthly_fee')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払期間（開始）</label>
                                    <input type="date" name="childsupport_start_payment" value="{{ $business->childsupport_start_payment }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_start_payment')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">養育費月額報酬</label>
                                    <input type="text" name="childsupport_monthly_remuneration"
                                        value="{{ $business->childsupport_monthly_remuneration }}"
                                        data-raw="{{ $business->childsupport_monthly_remuneration }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_monthly_remuneration')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">支払期間（終了）</label>
                                    <input type="date" name="childsupport_end_payment" value="{{ $business->childsupport_end_payment }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_end_payment')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">未回収金額</label>
                                    <input type="text" name="childsupport_notcollected_amount"
                                        value="{{ $business->childsupport_notcollected_amount }}"
                                        data-raw="{{ $business->childsupport_notcollected_amount }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_notcollected_amount')
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">依頼者送金額</label>
                                    <input type="text" name="childsupport_remittance_amount"
                                        value="{{ $business->childsupport_remittance_amount }}"
                                        data-raw="{{ $business->childsupport_remittance_amount }}"
                                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_remittance_amount')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">入金先口座</label>
                                    <input type="text" name="childsupport_deposit_account" value="{{ $business->childsupport_deposit_account }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_deposit_account')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">返金日</label>
                                    <input type="date" name="childsupport_repayment_date" value="{{ $business->childsupport_repayment_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_repayment_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">入金日</label>
                                    <input type="date" name="childsupport_deposit_date" value="{{ $business->childsupport_deposit_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_deposit_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">返金先口座の金融機関名</label>
                                    <input type="text" name="childsupport_financialinstitution_name" value="{{ $business->childsupport_financialinstitution_name }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_financialinstitution_name')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">振込元口座名義</label>
                                    <input type="text" name="childsupport_transfersource_name" value="{{ $business->childsupport_transfersource_name }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_transfersource_name')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">返金先口座名義</label>
                                    <input type="text" name="childsupport_refundaccount_name" value="{{ $business->childsupport_refundaccount_name }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('childsupport_refundaccount_name')
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="childsupport_temporary_payment" value="0">
                                        <input type="checkbox" name="childsupport_temporary_payment" value="1"
                                            {{ $business->childsupport_temporary_payment == 1 ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">臨時払いの有無</span>
                                    </label>
                                    @errorText('childsupport_temporary_payment')
                                </div>                                
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">備考</label>
                                    <textarea name="childsupport_memo" rows="3" class="mt-1 p-2 border rounded w-full bg-white required">{{ $business->childsupport_memo }}</textarea>
                                    @errorText('childsupport_memo')
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
                                <!-- 親：流入経路 -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>流入経路</label>
                                    <select id="route" name="route" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 未選択 --</option>
                                        @foreach (config('master.routes') as $key => $label)
                                            <option value="{{ $key }}" @selected($business->route == $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('route')
                                </div>
                            
                                <!-- 子：流入経路（詳細） -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">流入経路（詳細）</label>
                                    <select id="routedetail" name="routedetail" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 未選択 --</option>
                                        {{-- JSで上書き --}}
                                    </select>
                                    @errorText('routedetail')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者</label>
                                    <input type="text" name="introducer" value="{{ $business->introducer }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('introducer')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">紹介者その他</label>
                                    <input type="text" name="introducer_others" value="{{ $business->introducer_others }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('introducer_others')
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>コメント（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">コメント</label>
                                    <input type="text" name="comment" value="{{ $business->comment }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('comment')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">進捗コメント</label>
                                    <input type="text" name="progress_comment" value="{{ $business->progress_comment }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('progress_comment')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>期日メモ（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">期日メモ</label>
                                    <textarea name="duedate_memo" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $business->duedate_memo }}</textarea>
                                    @errorText('duedate_memo')
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

                                <!-- 編集不可メッセージ -->
                                <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-4 col-span-2">
                                    <p class="font-semibold">⚠️ 関係先は契約に関わる情報のため、編集はできません。</p>
                                    <p class="text-sm mt-1">
                                        修正が必要な場合は、<strong class="font-semibold">管理者に依頼して受任案件を削除</strong>し、
                                        <strong class="font-semibold">相談のステータスを戻して再登録</strong>してください。
                                    </p>
                                </div>
                            
                                <!-- クライアント（編集不可） -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント</label>
                                    <input type="text"
                                           class="w-full p-2 border rounded bg-gray-100 text-gray-700"
                                           value="{{ $business->client->name_kanji }}"
                                           disabled>
                                    <input type="hidden" name="client_id" value="{{ $business->client->id }}">
                                </div>
                            
                                <!-- 相談（編集不可） -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">相談</label>
                                    <input type="text"
                                           class="w-full p-2 border rounded bg-gray-100 text-gray-700"
                                           value="{!! optional($business->consultation)->title ?: '&nbsp;' !!}"
                                           disabled>
                                    <input type="hidden" name="consultation_id" value="{{ optional($business->consultation)->id }}">
                                </div>                      
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('business.show', $business->id) }}"
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

    // ▼ 流入経路、事件分野の動的更新
    const dynamicOptions = {
        routedetail: @json($routedetailOptions ?? []),
        casedetail: @json($casedetailOptions ?? []),
        // 他の動的セレクトがあればここに追加
    };

    function setupDependentSelect(parentId, childId, optionKey, selectedValue = null) {
        const parent = document.getElementById(parentId);
        const child = document.getElementById(childId);
        if (!parent || !child || !dynamicOptions[optionKey]) return;

        function update() {
            const selected = parent.value;
            const options = dynamicOptions[optionKey][selected] || [];
            child.innerHTML = '<option value="">-- 未選択 --</option>';
            options.forEach(opt => {
                const el = document.createElement('option');
                el.value = opt.id;
                el.textContent = opt.label;
                child.appendChild(el);
            });
            if (selectedValue) {
                child.value = selectedValue;
            }
        }

        parent.addEventListener('change', update);
        update(); // 初期化
    }

    // ▼ 呼び出し例（初期値も渡せる）
    setupDependentSelect(
        'route', 'routedetail',
        'routedetail',
        "{{ old('routedetail', optional($business ?? null)->routedetail) }}"
    );

    setupDependentSelect(
        'case_category', 'case_subcategory',
        'casedetail',
        "{{ old('case_subcategory', optional($business ?? null)->case_subcategory) }}"
    );
    // 他にも以下のように呼び出し可能にしておけば、JSは再利用できます
    // setupDependentSelect('court', 'court_branch', 'court_branch', old値...);

});
</script>
@endsection