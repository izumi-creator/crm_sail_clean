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
            <div class="text-md text-gray-100 mb-1">
                {{ $advisory->advisory_party == 1 ? '個人の顧問契約' : '法人の顧問契約' }}<span>　件名:</span>{!! $advisory->title ?: '&nbsp;' !!}
            </div>
            <div class="text-md font-bold">
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700 px-6 py-4">
            @if ($advisory->advisory_party == 1)

                <!-- 個人クライアント用表示 -->
                 {{-- 📌 左：主要情報 --}}
                <div class="border rounded shadow bg-white">
                    <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border-b">📌 主要情報</div>
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">電話番号（第一連絡先）:</div>
                        <div>
                            @if (!empty($advisory->client->first_contact_number))
                                <a href="tel:{{ $advisory->client->first_contact_number }}" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory->client->first_contact_number }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="font-semibold">メールアドレス1:</div>
                        <div>
                            @if (!empty($advisory->client->email1))
                                <a href="mailto:{{ $advisory->client->email1 }}" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory->client->email1 }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="font-semibold">担当弁護士:</div>
                        <div>{{ optional($advisory->lawyer)->name ?? '-' }}</div>
                        <div class="font-semibold">担当パラリーガル:</div>
                        <div>{{ optional($advisory->paralegal)->name ?? '-' }}</div>
                        <div class="font-semibold">ステータス:</div>
                        <div>{{ config('master.advisory_contracts_statuses')[$advisory->status] ?? '-' }}</div>
                        <div class="font-semibold">Googleフォルダ:</div>
                        <div>
                            @if (!empty($advisory->folder_id))
                                <a href="https://drive.google.com/drive/folders/{{ $advisory->folder_id }}" class="text-blue-600 underline" target="_blank" rel="noopener">フォルダを開く</a>
                            @else
                                （登録なし）
                            @endif
                        </div>
                        <div class="font-semibold">利益相反:</div>
                        <div>
                            @php
                                $confliction = $advisory->opponent_confliction ?? 0;
                                $conflictionDate = $advisory->opponent_confliction_date;
                                $labels = config('master.opponent_conflictions');
                                $colorClass = match ((int)$confliction) {
                                    1 => 'text-green-700',
                                    2 => 'text-red-700',
                                    3 => 'text-orange-600',
                                    default => 'text-gray-500',
                                };
                            @endphp
                            <span class="{{ $colorClass }}">{{ $labels[$confliction] ?? '未実施' }}</span>
                            @if ($conflictionDate)
                                <span class="ml-2 text-sm text-gray-600">（{{ \Carbon\Carbon::parse($conflictionDate)->format('Y/m/d') }} 実施）</span>
                            @endif
                            <a href="#" onclick="event.preventDefault(); document.getElementById('conflictModal').classList.remove('hidden');" class="ml-3 bg-blue-500 text-white text-xs px-2 py-1 rounded shadow">利益相反検索</a>
                        </div>
                    </div>
                </div>
            
                {{-- 📝 右：契約情報 --}}
                <div class="border rounded shadow bg-white">
                    <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border-b">📝 契約情報</div>
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">顧問料月額:</div>
                        <div>
                            {!! $advisory->amount_monthly !== null ? '¥' . number_format($advisory->amount_monthly) : '-' !!}
                        </div>
                        <div class="font-semibold">支払区分:</div>
                        <div>
                            {!! $advisory->payment_category ? config('master.payment_categories')[$advisory->payment_category] : '-' !!}
                        </div>
                        <div class="font-semibold">支払方法:</div>
                        <div>
                            {!! $advisory->payment_method ? config('master.payment_methods')[$advisory->payment_method] : '-' !!}
                        </div>
                        <div class="font-semibold">外部連携ID:</div>
                        <div>
                            {!! $advisory->external_id ? e($advisory->external_id) : '-' !!}
                        </div>
                    </div>
                </div>

            @else
                <!-- 法人クライアント用表示 -->
                 {{-- 📌 左：主要情報 --}}
                <div class="border rounded shadow bg-white">
                    <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border-b">📌 主要情報</div>
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">取引先責任者名:</div>
                        <div>
                             {{ optional($advisory->client)->contact_last_name_kanji }}　{{ optional($advisory->client)->contact_first_name_kanji }}
                             （{{ optional($advisory->client)->contact_last_name_kana }}　{{ optional($advisory->client)->contact_first_name_kana }}）
                        </div>
                        <div class="font-semibold">電話番号（第一連絡先）:</div>
                        <div>
                            @if (!empty($advisory->client->first_contact_number))
                                <a href="tel:{{ $advisory->client->first_contact_number }}" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory->client->first_contact_number }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="font-semibold">取引先責任者_メール1:</div>
                        <div>
                            @if (!empty($advisory->client->contact_email1))
                                <a href="mailto:{{ $advisory->client->contact_email1 }}" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory->client->contact_email1 }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="font-semibold">担当弁護士:</div>
                        <div>{{ optional($advisory->lawyer)->name ?? '-' }}</div>
                        <div class="font-semibold">担当パラリーガル:</div>
                        <div>{{ optional($advisory->paralegal)->name ?? '-' }}</div>
                        <div class="font-semibold">ステータス:</div>
                        <div>{{ config('master.advisory_contracts_statuses')[$advisory->status] ?? '-' }}</div>
                        <div class="font-semibold">Googleフォルダ:</div>
                        <div>
                            @if (!empty($advisory->folder_id))
                                <a href="https://drive.google.com/drive/folders/{{ $advisory->folder_id }}" class="text-blue-600 underline" target="_blank" rel="noopener">フォルダを開く</a>
                            @else
                                （登録なし）
                            @endif
                        </div>
                        <div class="font-semibold">利益相反:</div>
                        <div>
                            @php
                                $confliction = $advisory->opponent_confliction ?? 0;
                                $conflictionDate = $advisory->opponent_confliction_date;
                                $labels = config('master.opponent_conflictions');
                                $colorClass = match ((int)$confliction) {
                                    1 => 'text-green-700',
                                    2 => 'text-red-700',
                                    3 => 'text-orange-600',
                                    default => 'text-gray-500',
                                };
                            @endphp
                            <span class="{{ $colorClass }}">{{ $labels[$confliction] ?? '未実施' }}</span>
                            @if ($conflictionDate)
                                <span class="ml-2 text-sm text-gray-600">（{{ \Carbon\Carbon::parse($conflictionDate)->format('Y/m/d') }} 実施）</span>
                            @endif
                            <a href="#" onclick="event.preventDefault(); document.getElementById('conflictModal').classList.remove('hidden');" class="ml-3 bg-blue-500 text-white text-xs px-2 py-1 rounded shadow">利益相反検索</a>
                        </div>
                    </div>
                </div>
            
                {{-- 📝 右：契約情報 --}}
                <div class="border rounded shadow bg-white">
                    <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border-b">📝 契約情報</div>
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">顧問料月額:</div>
                        <div>
                            {!! $advisory->amount_monthly !== null ? '¥' . number_format($advisory->amount_monthly) : '-' !!}
                        </div>
                        <div class="font-semibold">支払区分:</div>
                        <div>
                            {!! $advisory->payment_category ? config('master.payment_categories')[$advisory->payment_category] : '-' !!}
                        </div>
                        <div class="font-semibold">支払方法:</div>
                        <div>
                            {!! $advisory->payment_method ? config('master.payment_methods')[$advisory->payment_method] : '-' !!}
                        </div>
                        <div class="font-semibold">外部連携ID:</div>
                        <div>
                            {!! $advisory->external_id ? e($advisory->external_id) : '-' !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ▼ タスク表示：完了と未完了に分割（関係者タブ風） --}}
    <div class="space-y-6">
    
        {{-- ✅ タスク履歴（完了） --}}
        <div class="border rounded shadow bg-white">
            <div class="flex justify-between items-center px-4 py-2 bg-sky-700 text-white rounded-t">
                <div class="font-bold text-sm">✅ タスク履歴（完了）※取り下げは除く</div>
            </div>
            <div class="px-4 py-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-200 text-blue-900">
                        <tr>
                            <th class="px-2 py-1 border">件名</th>
                            <th class="px-2 py-1 border">作成日</th>
                            <th class="px-2 py-1 border">宛先</th>
                            <th class="px-2 py-1 border">内容</th>
                            <th class="px-2 py-1 border">orderer</th>
                            <th class="px-2 py-1 border">worker</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doneTasks as $task)
                            <tr>
                                <td class="border px-2 py-1">
                                    <a href="{{ route('task.show', $task->id) }}" class="text-blue-600 hover:underline">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td class="border px-2 py-1">{{ $task->created_at->format('Y-m-d H:i') }}</td>
                                <td class="border px-2 py-1">{{ $task->record_to ?? '-' }}</td>
                                <td class="border px-2 py-1 whitespace-pre-wrap break-words max-w-sm">{{ $task->content }}</td>
                                <td class="border px-2 py-1">{{ $task->orderer->name ?? '-' }}</td>
                                <td class="border px-2 py-1">{{ $task->worker->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                        {{-- 件数0の場合の表示 --}}
                        @if($doneTasks->isEmpty())
                            <tr>
                                <td class="px-2 py-2 text-center text-gray-500 border" colspan="6">完了タスクはありません。</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    
        {{-- 📌 活動予定（未完了） --}}
        <div class="border rounded shadow bg-white">
            <div class="flex justify-between items-center px-4 py-2 bg-sky-700 text-white rounded-t">
                <div class="font-bold text-sm">📌 未完了タスク</div>
                <div class="space-x-2">
                    <a href="{{ route('task.create', ['related_party' => 3, 'advisory_contract_id' => $advisory->id, 'redirect_url' => url()->current()]) }}"
                       class="bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded">
                        ＋新規ToDo
                    </a>
                    <a href="{{ route('task.create.phone', ['related_party' => 3, 'advisory_contract_id' => $advisory->id, 'redirect_url' => url()->current()]) }}"
                       class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded">
                        ＋発着信ToDo
                    </a>
                </div>
            </div>
            <div class="px-4 py-3 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-200 text-blue-900">
                        <tr>
                            <th class="px-2 py-1 border">件名</th>
                            <th class="px-2 py-1 border">作成日</th>
                            <th class="px-2 py-1 border">期限</th>
                            <th class="px-2 py-1 border">ステータス</th>
                            <th class="px-2 py-1 border">内容</th>
                            <th class="px-2 py-1 border">orderer</th>
                            <th class="px-2 py-1 border">worker</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todoTasks as $task)
                            <tr>
                                <td class="border px-2 py-1">
                                    <a href="{{ route('task.show', $task->id) }}" class="text-blue-600 hover:underline">
                                        {{ $task->title }}
                                    </a>
                                </td>
                                <td class="border px-2 py-1">{{ $task->created_at->format('Y-m-d H:i') }}</td>
                                <td class="border px-2 py-1">{{ $task->deadline_date }}</td>
                                <td class="border px-2 py-1">{{ config('master.task_statuses')[$task->status] ?? '-' }}</td>
                                <td class="border px-2 py-1 whitespace-pre-wrap break-words max-w-sm">{{ $task->content }}</td>
                                <td class="border px-2 py-1">{{ $task->orderer->name ?? '-' }}</td>
                                <td class="border px-2 py-1">{{ $task->worker->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                        {{-- 件数0の場合の表示 --}}
                        @if($todoTasks->isEmpty())
                            <tr>
                                <td class="px-2 py-2 text-center text-gray-500 border" colspan="5">予定タスクはありません。</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
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
            {{-- <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-task">
                タスク一覧（{{ $advisory->tasks->count() }}件）
            </button> --}}
            {{-- <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-negotiations">
                折衝履歴一覧（{{ $advisory->negotiations->count() }}件）
            </button> --}}
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-documents">
                会計一覧（ダミー）
            </button>
        </div>
    </div>


    <!-- ▼ 詳細情報タブ（今ある内容を全部この中に入れる） -->
    <div id="tab-detail" class="tab-content">

        <!-- 顧問契約詳細カード -->
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
                        <label class="block text-sm font-semibold text-gray-700 mb-1">件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $advisory->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス</label>
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
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">外部連携ID</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory->external_id ?: '&nbsp;' !!}
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
                                        {!! $advisory->office_id ? config('master.offices_id')[$advisory->office_id] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">GoogleDriveフォルダID</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $advisory->folder_id ?: '&nbsp;' !!}</div>
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
                <a href="{{ route('advisory_consultation.create', [
                    'advisory_contract_id' => $advisory->id,
                    'client_id' => $advisory->client_id,
                    'redirect_url' => route('advisory.show', ['advisory' => $advisory->id]) . '#tab-advisory_consultations'
                ]) }}"
                class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
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

    <!-- ▼ タスク一覧タブ -->
    <div id="tab-task" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
        <div class="mb-4 flex justify-end space-x-2">
            <a href="{{ route('task.create', [
                'related_party' => 3,
                'advisory_contract_id' => $advisory->id,
                'redirect_url' => route('advisory.show', ['advisory' => $advisory->id]) . '#tab-task'
            ]) }}"
            class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
        </div>
            @if ($advisory->tasks->isEmpty())
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
                        @foreach ($advisory->tasks as $task)
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
                'related_party' => 3,
                'advisory_contract_id' => $advisory->id,
                'redirect_url' => route('advisory.show', ['advisory' => $advisory->id]) . '#tab-negotiations'
            ]) }}"
            class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
        </div>
            @if ($advisory->negotiations->isEmpty())
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
                        @foreach ($advisory->negotiations as $negotiation)
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
            
                <input type="hidden" name="_modal" value="edit">
                <input type="hidden" name="opponent_confliction" value="{{ $advisory->opponent_confliction }}">

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
                        <textarea name="explanation" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $advisory->explanation }}</textarea>
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
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>取扱事務所</label>
                                    <select name="office_id" class="mt-1 p-2 border rounded w-full bg-white required">
                                        <option value="">-- 選択してください --</option>
                                        @foreach (config('master.offices_id') as $key => $value)
                                            <option value="{{ $key }}" {{ $advisory->office_id == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('office_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">GoogleDriveフォルダID</label>
                                    <input type="text" name="folder_id" 
                                    placeholder="例：1A2B3C4D5E6F7G8H9I0J"
                                    value="{{ $advisory->folder_id }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('folder_id')
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
                                <!-- 親：ソース -->
                                <div>
                                    <label class="block font-semibold mb-1">ソース</label>
                                    <select id="source" name="source" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 未選択 --</option>
                                        @foreach (config('master.routes') as $key => $label)
                                            <option value="{{ $key }}" @selected($advisory->source == $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('source')
                                </div>                            
                                <!-- 子：ソース（詳細） -->
                                <div>
                                    <label class="block font-semibold mb-1">ソース（詳細）</label>
                                    <select id="source_detail" name="source_detail" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 未選択 --</option>
                                        {{-- JSで上書き --}}
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

    <!-- 利益相反チェックモーダル -->
    <div id="conflictModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('advisory.conflict.update', $advisory->id) }}">
                @csrf
            
                <input type="hidden" name="_modal" value="conflict">

                <!-- 見出し -->
                <div class="bg-blue-600 text-white px-4 py-2 font-bold border-b">利益相反チェック</div>


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

                <div class="bg-blue-50 border-blue-300 text-gray-800 text-sm rounded px-4 py-3 mb-4">
                    <p class="mb-1">以下の情報をもとに一致候補を自動抽出しています：</p>
                    <ul class="list-disc list-inside pl-2 space-y-1">
                        <li>
                            クライアント一致候補</span>は
                            <span class="font-semibold">クライアント名（漢字・かな）</span>または
                            <span class="font-semibold">取引責任者（漢字・かな）</span>と一致したものです。
                        </li>
                        <li>
                            関係者一致候補</span>は
                            <span class="font-semibold">関係者名（漢字・かな）</span>または
                            <span class="font-semibold">担当者名（漢字・かな）</span>と一致したものです。
                        </li>
                        <li>
                            該当する候補が表示された場合は
                            <span class="font-semibold">詳細画面で内容をご確認の上、利益相反確認結果を入力</span>してください。
                        </li>
                    </ul>
                </div>

                <!-- 内容 -->
                <div class="px-6 py-4 text-sm text-gray-800">
                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-700">
                            クライアント一致候補（{{ count($matchedClients) }}件）
                        </h3>
                        @if (count($matchedClients) > 0)
                            <ul class="list-disc pl-6 text-sm text-gray-600">
                                @foreach ($matchedClients as $client)
                                    <li>
                                        {{ $client->name_kanji }}（{{ $client->name_kana }}）
                                        <a href="{{ route('client.show', $client->id) }}" class="text-blue-500 ml-2" target="_blank">詳細</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 mt-1">一致するクライアントはありません。</p>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-700">
                            関係者一致候補（{{ count($matchedRelatedParties) }}件）
                        </h3>
                        @if (count($matchedRelatedParties) > 0)
                            <ul class="list-disc pl-6 text-sm text-gray-600">
                                @foreach ($matchedRelatedParties as $rp)
                                    <li>
                                        {{ $rp->relatedparties_name_kanji }}（{{ $rp->relatedparties_name_kana }}）
                                        <a href="{{ route('relatedparty.show', $rp->id) }}" class="text-blue-500 ml-2" target="_blank">詳細</a>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500 mt-1">一致する関係者はありません。</p>
                        @endif
                    </div>
                
                    {{-- 結果入力 --}}
                    <div class="mb-4">
                        <label class="block font-semibold text-sm mb-1">利益相反確認結果</label>
                        <select name="opponent_confliction" class="w-full border rounded p-2">
                            <option value="">-- 選択してください --</option>
                            @foreach(config('master.opponent_conflictions') as $key => $label)
                                @if($key != '0')
                                    <option value="{{ $key }}" {{ $advisory->opponent_confliction == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    @if ($advisory->opponent_confliction_date)
                        <div class="mb-4">
                            <label class="block font-semibold text-sm text-gray-700 mb-1">前回実施日</label>
                            <div class="p-2 bg-gray-100 border rounded text-sm">
                                {{ \Carbon\Carbon::parse($advisory->opponent_confliction_date)->format('Y/m/d') }}
                            </div>
                        </div>
                    @endif
                    
                    <div class="mb-4">
                        <label class="block font-semibold text-sm mb-1">実施日</label>
                        <input type="text" class="w-full p-2 border rounded bg-gray-100" value="{{ now()->format('Y-m-d') }}" readonly>
                    </div>
                </div>
            
                <!-- フッター -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('advisory.show', $advisory->id) }}"
                       class="px-4 py-2 bg-gray-300 text-black rounded min-w-[100px] text-center">
                       キャンセル
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 min-w-[100px]">登録</button>
                </div>
            </form>
        </div>
    </div>            
@endsection

@section('scripts')
@if ($errors->any())
<script>
    window.addEventListener('load', function () {
        const modal = '{{ old('_modal') }}';

        if (modal === 'edit') {
            document.getElementById('editModal')?.classList.remove('hidden');
        }
        if (modal === 'conflict') {
            document.getElementById('conflictModal')?.classList.remove('hidden');
        }

        // 共通：アコーディオン展開（どちらでも有効にして問題なし）
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

    // ▼ 流入経路（ソース）の動的更新
    const dynamicOptions = {
        routedetail: @json($routedetailOptions ?? []),
        // ここに court_branch など他の構成も後で追加できる
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
        'source', 'source_detail',
        'routedetail',
        "{{ old('source_detail', optional($advisory ?? null)->source_detail) }}"
    );

    // 他にも以下のように呼び出し可能にしておけば、JSは再利用できます
    // setupDependentSelect('court', 'court_branch', 'court_branch', old値...);

});
</script>
@endsection