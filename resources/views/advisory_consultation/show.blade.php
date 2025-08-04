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
            <div class="text-md text-gray-100 mb-1">
                {{ $advisory_consultation->advisory_party == 1 ? '個人の顧問相談' : '法人の顧問相談' }}<span>　件名:</span>{!! $advisory_consultation->title ?: '&nbsp;' !!}
            </div>
            <div class="text-md font-bold">
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-gray-700 px-6 py-4">
            @if ($advisory_consultation->advisory_party == 1)
            
                {{-- 📌 左：主要情報 --}}
                <div class="border rounded shadow bg-white">
                    <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border-b">📌 主要情報</div>
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">電話番号（第一連絡先）:</div>
                        <div>
                            @if (!empty($advisory_consultation->client->first_contact_number))
                                <a href="tel:{{ $advisory_consultation->client->first_contact_number }}" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory_consultation->client->first_contact_number }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="font-semibold">メールアドレス1:</div>
                        <div>
                            @if (!empty($advisory_consultation->client->email1))
                                <a href="mailto:{{ $advisory_consultation->client->email1 }}" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory_consultation->client->email1 }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="font-semibold">担当弁護士:</div>
                        <div>{{ optional($advisory_consultation->lawyer)->name ?? '-' }}</div>
                        <div class="font-semibold">担当パラリーガル:</div>
                        <div>{{ optional($advisory_consultation->paralegal)->name ?? '-' }}</div>
                        <div class="font-semibold">ステータス:</div>
                        <div>{{ config('master.advisory_consultations_statuses')[$advisory_consultation->status] ?? '-' }}</div>
                        <div class="font-semibold">Googleフォルダ:</div>
                        <div>
                            @if (!empty($advisory_consultation->folder_id))
                                <a href="https://drive.google.com/drive/folders/{{ $advisory_consultation->folder_id }}" class="text-blue-600 underline" target="_blank" rel="noopener">フォルダを開く</a>
                            @else
                                （登録なし）
                            @endif
                        </div>
                        <div class="font-semibold">利益相反:</div>
                        <div>
                            @php
                                $confliction = $advisory_consultation->opponent_confliction ?? 0;
                                $conflictionDate = $advisory_consultation->opponent_confliction_date;
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
            
                {{-- 👤 右：相手方情報 --}}
                <div class="border rounded shadow bg-white">
                    <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border-b">👤 相手方情報</div>
                    @php
                        $targetParty1 = $advisory_consultation->relatedParties->firstWhere('relatedparties_type', 3);
                    @endphp
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">相手方代理人:</div>
                        <div>
                            {{ $targetParty1->relatedparties_name_kanji ?? '-' }}
                            @if (!empty($targetParty1?->relatedparties_name_kana))
                                （{{ $targetParty1->relatedparties_name_kana }}）
                            @endif
                        </div>
                        <div class="font-semibold">相手方代理人（電話）:</div>
                        <div>
                            @if (!empty($targetParty1?->phone_number))
                                <a href="tel:{{ $targetParty1->phone_number }}" class="text-blue-600 underline">{{ $targetParty1->phone_number }}</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    @php
                        $targetParty2 = $advisory_consultation->relatedParties->firstWhere('relatedparties_type', 2);
                    @endphp
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">相手方本人（氏名）:</div>
                        <div>
                            {{ $targetParty2->relatedparties_name_kanji ?? '-' }}
                            @if (!empty($targetParty2?->relatedparties_name_kana))
                                （{{ $targetParty2->relatedparties_name_kana }}）
                            @endif
                        </div>
                        <div class="font-semibold">相手方本人（電話）:</div>
                        <div>
                            @if (!empty($targetParty2?->phone_number))
                                <a href="tel:{{ $targetParty2->phone_number }}" class="text-blue-600 underline">{{ $targetParty2->phone_number }}</a>
                            @else
                                -
                            @endif
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
                             {{ optional($advisory_consultation->client)->contact_last_name_kanji }}　{{ optional($advisory_consultation->client)->contact_first_name_kanji }}
                             （{{ optional($advisory_consultation->client)->contact_last_name_kana }}　{{ optional($advisory_consultation->client)->contact_first_name_kana }}）
                        </div>
                        <div class="font-semibold">電話番号（第一連絡先）:</div>
                        <div>
                            @if (!empty($advisory_consultation->client->first_contact_number))
                                <a href="tel:{{ $advisory_consultation->client->first_contact_number }}" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory_consultation->client->first_contact_number }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="font-semibold">取引先責任者_メール1:</div>
                        <div>
                            @if (!empty($advisory_consultation->client->contact_email1))
                                <a href="mailto:{{ $advisory_consultation->client->contact_email1 }}" class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory_consultation->client->contact_email1 }}
                                </a>
                            @else
                                -
                            @endif
                        </div>
                        <div class="font-semibold">担当弁護士:</div>
                        <div>{{ optional($advisory_consultation->lawyer)->name ?? '-' }}</div>
                        <div class="font-semibold">担当パラリーガル:</div>
                        <div>{{ optional($advisory_consultation->paralegal)->name ?? '-' }}</div>
                        <div class="font-semibold">ステータス:</div>
                        <div>{{ config('master.advisory_consultations_statuses')[$advisory_consultation->status] ?? '-' }}</div>
                        <div class="font-semibold">Googleフォルダ:</div>
                        <div>
                            @if (!empty($advisory_consultation->folder_id))
                                <a href="https://drive.google.com/drive/folders/{{ $advisory_consultation->folder_id }}" class="text-blue-600 underline" target="_blank" rel="noopener">フォルダを開く</a>
                            @else
                                （登録なし）
                            @endif
                        </div>
                        <div class="font-semibold">利益相反:</div>
                        <div>
                            @php
                                $confliction = $advisory_consultation->opponent_confliction ?? 0;
                                $conflictionDate = $advisory_consultation->opponent_confliction_date;
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
            
                {{-- 👤 右：相手方情報 --}}
                <div class="border rounded shadow bg-white">
                    <div class="bg-blue-100 text-blue-900 px-4 py-2 font-bold border-b">👤 相手方情報</div>
                    @php
                        $targetParty1 = $advisory_consultation->relatedParties->firstWhere('relatedparties_type', 3);
                    @endphp
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">相手方代理人:</div>
                        <div>
                            {{ $targetParty1->relatedparties_name_kanji ?? '-' }}
                            @if (!empty($targetParty1?->relatedparties_name_kana))
                                （{{ $targetParty1->relatedparties_name_kana }}）
                            @endif
                        </div>
                        <div class="font-semibold">相手方代理人（電話）:</div>
                        <div>
                            @if (!empty($targetParty1?->phone_number))
                                <a href="tel:{{ $targetParty1->phone_number }}" class="text-blue-600 underline">{{ $targetParty1->phone_number }}</a>
                            @else
                                -
                            @endif
                        </div>
                    </div>
                    @php
                        $targetParty2 = $advisory_consultation->relatedParties->firstWhere('relatedparties_type', 2);
                    @endphp
                    <div class="p-4 grid grid-cols-[auto,1fr] gap-x-4 gap-y-2 text-sm text-gray-700">
                        <div class="font-semibold">相手方本人（氏名）:</div>
                        <div>
                            {{ $targetParty2->relatedparties_name_kanji ?? '-' }}
                            @if (!empty($targetParty2?->relatedparties_name_kana))
                                （{{ $targetParty2->relatedparties_name_kana }}）
                            @endif
                        </div>
                        <div class="font-semibold">相手方本人（電話）:</div>
                        <div>
                            @if (!empty($targetParty2?->phone_number))
                                <a href="tel:{{ $targetParty2->phone_number }}" class="text-blue-600 underline">{{ $targetParty2->phone_number }}</a>
                            @else
                                -
                            @endif
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
                    <a href="{{ route('task.create', ['related_party' => 4, 'advisory_consultation_id' => $advisory_consultation->id, 'redirect_url' => url()->current()]) }}"
                       class="bg-green-500 hover:bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded">
                        ＋新規ToDo
                    </a>
                    <a href="{{ route('task.create.phone', ['related_party' => 4, 'advisory_consultation_id' => $advisory_consultation->id, 'redirect_url' => url()->current()]) }}"
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
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-relatedparty">
                関係者一覧（{{ $advisory_consultation->relatedParties->count() }}件）
            </button>
            {{-- <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-task">
                タスク一覧（{{ $advisory_consultation->tasks->count() }}件）
            </button> --}}
            {{-- <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-negotiations">
                折衝履歴一覧（{{ $advisory_consultation->negotiations->count() }}件）
            </button> --}}
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
                        顧問契約
                    </div>
                    <div class="col-span-2">
                        <label class="font-bold">顧問契約：件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                            @if ($advisory_consultation->advisoryContract)
                                <a href="{{ route('advisory.show', $advisory_consultation->advisoryContract->id) }}"
                                   class="text-blue-600 underline hover:text-blue-800">
                                    {{ $advisory_consultation->advisoryContract->title }}
                                </a>
                            @elseif ($advisory_consultation->advisory_contract_id)
                                <span class="text-gray-400">（削除された顧問契約）</span>
                            @else
                                {{-- 空白（何も表示しない） --}}
                                &nbsp;
                            @endif
                        </div>
                    </div>
                    <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                        基本情報
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $advisory_consultation->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                            {!! $advisory_consultation->status ? config('master.advisory_consultations_statuses')[$advisory_consultation->status] : '&nbsp;' !!}
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">相談概要</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $advisory_consultation->case_summary }}</pre>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $advisory_consultation->special_notes }}</pre>
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
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">取扱事務所</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        {!! $advisory_consultation->office_id ? config('master.offices_id')[$advisory_consultation->office_id] : '&nbsp;' !!}
                                    </div>
                                </div>
                                <div></div>
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

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>関連情報（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="font-bold">相談に移行</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">
                                        @if ($advisory_consultation->consultation)
                                            <a href="{{ route('consultation.show', $advisory_consultation->consultation->id) }}"
                                               class="text-blue-600 underline hover:text-blue-800">
                                                {{ $advisory_consultation->consultation->title }}
                                            </a>
                                        @elseif ($advisory_consultation->consultation_id)
                                            <span class="text-gray-400">（削除された相談）</span>
                                        @else
                                            <span class="text-gray-400">（移行されていません）</span>
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
                    <a href="{{ route('advisory_consultation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ▼ 関係者一覧タブ -->
    <div id="tab-relatedparty" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <div class="mb-4 flex justify-end space-x-2">
                <a href="{{ route('relatedparty.create', [
                    'advisory_consultation_id' => $advisory_consultation->id,
                    'redirect_url' => route('advisory_consultation.show', ['advisory_consultation' => $advisory_consultation->id]) . '#tab-relatedparty'
                ]) }}"
                class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>   
            </div>
            @if ($advisory_consultation->relatedParties->isEmpty())
                <p class="text-sm text-gray-500">関係者は登録されていません。</p>
            @else
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="bg-sky-700 text-white text-sm shadow-md">
                        <tr>
                            <th class="border p-2 w-1/12">ID</th>
                            <th class="border p-2 w-3/12">関係者名（漢字）</th>
                            <th class="border p-2 w-1/12">区分</th>
                            <th class="border p-2 w-1/12">分類</th>
                            <th class="border p-2 w-1/12">種別</th>
                            <th class="border p-2 w-1/12">立場</th>
                            <th class="border p-2 w-2/12">電話</th>
                            <th class="border p-2 w-2/12">メール</th>
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
                            <td class="border px-2 py-[6px] truncate">
                                {!! $relatedparty->phone_number
                                ? '<a href="tel:' . e($relatedparty->phone_number) . '" class="text-blue-600 underline hover:text-blue-800">' . e($relatedparty->phone_number) . '</a>'
                                : '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! $relatedparty->email
                                ? '<a href="mailto:' . e($relatedparty->email) . '" class="text-blue-600 underline hover:text-blue-800">' . e($relatedparty->email) . '</a>'
                                : '&nbsp;' !!}
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
                'related_party' => 4,
                'advisory_consultation_id' => $advisory_consultation->id,
                'redirect_url' => route('advisory_consultation.show', ['advisory_consultation' => $advisory_consultation->id]) . '#tab-task'
            ]) }}"
            class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
        </div>
            @if ($advisory_consultation->tasks->isEmpty())
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
                        @foreach ($advisory_consultation->tasks as $task)
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
                'related_party' => 4,
                'advisory_consultation_id' => $advisory_consultation->id,
                'redirect_url' => route('advisory_consultation.show', ['advisory_consultation' => $advisory_consultation->id]) . '#tab-negotiations'
            ]) }}"
            class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
        </div>
            @if ($advisory_consultation->negotiations->isEmpty())
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
                        @foreach ($advisory_consultation->negotiations as $negotiation)
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

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('advisory_consultation.update', $advisory_consultation->id) }}">
                @csrf
                @method('PUT')

                <input type="hidden" name="_modal" value="edit">
                <input type="hidden" name="opponent_confliction" value="{{ $advisory_consultation->opponent_confliction }}">
                
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
                     <div class="col-span-2 bg-blue-50 border border-blue-300 text-blue-800 text-sm rounded px-4 py-3 mb-2">
                        <p class="mt-1">
                            ステータスを「相談（受任案件）へ移行」に変更すると、<strong>相談が自動作成</strong>されます。<br>
                            また、関係者が設定されている場合は、<strong>相談にも自動で紐づけ</strong>されます。<br>
                            すでに作成済みの場合は作成・紐づけはされません。<br>                           
                        </p>
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
                        <textarea name="case_summary" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $advisory_consultation->case_summary }}</textarea>
                        @errorText('case_summary')
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                        <textarea name="special_notes" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $advisory_consultation->special_notes }}</textarea>
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
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>取扱事務所</label>
                                    <select name="office_id" class="mt-1 p-2 border rounded w-full bg-white required">
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
                                    <select name="advisory_contract_id"
                                            class="select-advisory-edit w-full"
                                            data-initial-id="{{ $advisory_consultation->advisoryContract->id }}"
                                            data-initial-text="{{ optional($advisory_consultation->advisoryContract)->title }}">
                                        <option></option>
                                    </select>
                                    @errorText('advisory_contract_id')
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

    <!-- 利益相反チェックモーダル -->
    <div id="conflictModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('advisory_consultation.conflict.update', $advisory_consultation->id) }}">
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
                                    <option value="{{ $key }}" {{ $advisory_consultation->opponent_confliction == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    @if ($advisory_consultation->opponent_confliction_date)
                        <div class="mb-4">
                            <label class="block font-semibold text-sm text-gray-700 mb-1">前回実施日</label>
                            <div class="p-2 bg-gray-100 border rounded text-sm">
                                {{ \Carbon\Carbon::parse($advisory_consultation->opponent_confliction_date)->format('Y/m/d') }}
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
                    <a href="{{ route('advisory_consultation.show', $advisory_consultation->id) }}"
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
});
</script>
@endsection