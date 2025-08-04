@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">裁判所対応詳細</h2>

    <!-- 裁判所対応詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            @if (auth()->user()->role_type == 1)
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
            @endif
        </div>

        <!-- ✅ 裁判所対応情報の見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- 見出し -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border">裁判所対応情報</div>

            <!-- 内容 -->
            <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    基本情報
                </div>
                <!-- ステータス -->
               <div>
                   <label class="block text-sm font-semibold text-gray-700 mb-1">
                       <span class="text-red-500">*</span> ステータス
                  </label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! config('master.court_tasks_statuses')[$court_task->status] ?? '&nbsp;' !!}
                     </div>
                </div>
                <!-- ステータス詳細 -->
                <div>
                    <label class="font-bold">ステータス詳細</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->status_detail ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- タスク名 -->
                <div>
                   <label class="block text-sm font-semibold text-gray-700 mb-1">
                       <span class="text-red-500">*</span> タスク名
                    </label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->task_title ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- タスク分類 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span> タスク分類
                    </label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! config('master.court_task_categories')[$court_task->task_category] ?? '&nbsp;' !!}
                        </div>
                </div>
                <!-- 事件番号 -->
                <div>
                    <label class="font-bold">事件番号</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->case_number ?: '&nbsp;' !!}
                    </div>
                </div>
                <div></div>
                <!-- タスク内容 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">タスク内容</label>
                    <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $court_task->task_content }}</pre>
                </div>
                <!-- 担当弁護士 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($court_task->lawyer)->name ?: '&nbsp;' !!}</div>
                </div>
                <!-- 担当パラリーガル -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($court_task->paralegal)->name ?: '&nbsp;' !!}</div>
                </div>
                <!-- 期限 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">期限</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {{ $court_task->deadline ? $court_task->deadline->format('Y-m-d H:i') : '―' }}
                    </div>
                </div>
                <!-- 受任案件：件名 -->
                <div>
                    <label class="font-bold">受任案件：件名</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        @if ($court_task->business)
                            <a href="{{ route('business.show', $court_task->business->id) }}"
                               class="text-blue-600 underline hover:text-blue-800">
                                {{ $court_task->business->title }}
                            </a>
                        @elseif ($court_task->business_id)
                            <span class="text-gray-400">（削除された受任案件）</span>
                        @else
                            {{-- 空白（何も表示しない） --}}
                            &nbsp;
                        @endif
                    </div>
                </div>
                <!-- 移動時間 -->         
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"> 移動時間</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {{ $court_task->move_time
                            ? \Carbon\Carbon::createFromFormat('H:i:s', $court_task->move_time)->format('H:i')
                            : '―' }}
                    </div>
                </div>
                <!-- メモ欄 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">メモ欄</label>
                    <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $court_task->memo }}</pre>
                </div>

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    裁判所情報
                </div>

                <!-- 裁判所名 -->
                <div>
                    <label class="font-bold"><span class="text-red-500">*</span> 裁判所名</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        @if ($court_task->court)
                            <a href="{{ route('court.show', $court_task->court->id) }}"
                               class="text-blue-600 underline hover:text-blue-800">
                                {{ $court_task->court->court_name }}
                            </a>
                        @elseif ($court_task->court_id)
                            <span class="text-gray-400">（削除された裁判所）</span>
                        @else
                            {{-- 空白（何も表示しない） --}}
                            &nbsp;
                        @endif
                    </div>
                </div>
                <!-- 担当係 -->
                <div>
                    <label class="font-bold">担当係</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->department ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- 担当裁判官 -->
                <div>
                    <label class="font-bold">担当裁判官</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->judge ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- 担当書記官 -->
                <div>
                    <label class="font-bold">担当書記官</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->clerk ?: '&nbsp;' !!}
                     </div>
                </div>
                <!-- 電話（直通） -->
                <div>
                    <label class="font-bold">電話（直通）</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->tel_direct ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- FAX（直通） -->
                <div>
                    <label class="font-bold">FAX（直通）</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->fax_direct ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- メール（直通） -->
                <div>
                    <label class="font-bold">メール（直通）</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $court_task->email_direct ?: '&nbsp;' !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- ✅ 外枠の外に表示 -->
        <div class="relative mt-6 h-10">
           <!-- 左側：一覧に戻る -->
            <div class="absolute left-0">
                <a href="{{ route('business.show', ['business' => $court_task->business->id]) }}#tab-courtTask"
                   class="text-blue-600 hover:underline hover:text-blue-800">
                    一覧に戻る
                </a>
            </div>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('court_task.update', $court_task->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">裁判所対応編集</div>

                <!-- ✅ エラーボックスをgrid外に出す -->
                @if ($errors->any())
                <div class="p-6 pt-4 text-sm">
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
                    <!-- 区分 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> ステータス
                        </label>
                        <select name="status" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.court_tasks_statuses') as $key => $label)
                                <option value="{{ $key }}" @selected($court_task->status == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('status')
                    </div>
                    <!-- ステータス詳細 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス詳細</label>
                        <input type="text" name="status_detail" value="{{ $court_task->status_detail }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('status_detail')
                    </div>
                    <!-- タスク名 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> タスク名
                        </label>
                        <input type="text" name="task_title" value="{{ $court_task->task_title }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('task_title')
                    </div>
                    <!-- タスク分類 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> タスク分類
                        </label>
                        <select name="task_category" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.court_task_categories') as $key => $label)
                                <option value="{{ $key }}" @selected($court_task->task_category == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('task_category')
                    </div>
                    <!-- 事件番号 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">事件番号</label>
                        <input type="text" name="case_number" value="{{ $court_task->case_number }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('case_number')
                    </div>
                    <!-- タスク内容 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">タスク内容</label>
                        <textarea name="task_content" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $court_task->task_content }}</textarea>
                        @errorText('task_content')
                    </div>
                    <!-- 担当弁護士 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                        <select name="lawyer_id"
                                class="select-user-edit w-full"
                                data-initial-id="{{ $court_task->lawyer_id }}"
                                data-initial-text="{{ optional($court_task->lawyer)->name }}">
                            <option></option>
                        </select>
                        @errorText('lawyer_id')
                    </div>
                    <!-- 担当パラリーガル -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
                        <select name="paralegal_id"
                                class="select-user-edit w-full"
                                data-initial-id="{{ $court_task->paralegal_id }}"
                                data-initial-text="{{ optional($court_task->paralegal)->name }}">
                            <option></option>
                        </select>
                        @errorText('paralegal_id')
                    </div>
                    <!-- 期限 -->
                    <div>
                        <label class="block font-semibold mb-1">期限：年月日</label>
                        <input type="date" name="deadline_date"
                               value="{{ $court_task->deadline ? $court_task->deadline->format('Y-m-d') : '' }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('deadline_date')
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">期限：時間</label>
                        <select name="deadline_time" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 時間を選択 --</option>
                            @for ($h = 9; $h <= 20; $h++)
                                @foreach (['00', '15', '30', '45'] as $m)
                                    @php
                                        $time = sprintf('%02d:%s', $h, $m);
                                        $selected = $court_task->deadline && $court_task->deadline->format('H:i') === $time;
                                    @endphp
                                    <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>
                                        {{ $time }}
                                    </option>
                                @endforeach
                            @endfor
                        </select>
                        @errorText('deadline_time')
                    </div>
                    <!-- 受任案件（編集不可） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>受任案件</label>
                        <input type="text"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-700"
                               value="{!! optional($court_task->business)->title ?: '&nbsp;' !!}"
                               disabled>
                        <input type="hidden" name="business_id" value="{{ optional($court_task->business)->id }}">
                    </div>
                    <!-- 移動時間 --> 
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">移動時間</label>
                        <select name="move_time" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 時間を選択 --</option>
                            @for ($h = 9; $h <= 20; $h++)
                                @foreach (['00', '15', '30', '45'] as $m)
                                    @php
                                        $time = sprintf('%02d:%s', $h, $m);
                                        $selected = false;
                                        if ($court_task->move_time) {
                                            $move_time = \Carbon\Carbon::createFromFormat('H:i:s', $court_task->move_time)->format('H:i');
                                            $selected = $move_time === $time;
                                        }
                                    @endphp
                                    <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>
                                        {{ $time }}
                                    </option>
                                @endforeach
                            @endfor
                        </select>
                        @errorText('move_time')
                    </div>
                    <!-- メモ欄 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">メモ欄</label>
                        <textarea name="memo" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $court_task->memo }}</textarea>
                        @errorText('memo')
                    </div>
                    <div class="col-span-2 bg-orange-300 py-2 px-6 -mx-6">
                        裁判所情報    
                    </div>

                    <!-- 裁判所名 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>裁判所名</label>
                        <select name="court_id"
                                class="select-court-edit w-full"
                                data-initial-id="{{ $court_task->court_id }}"
                                data-initial-text="{{ optional($court_task->court)->court_name }}">
                            <option></option>
                        </select>
                        @errorText('court_id')
                    </div>
                    <!-- 担当係 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当係</label>
                        <input type="text" name="department" value="{{ $court_task->department }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('department')
                    </div>
                    <!-- 担当裁判官 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当裁判官</label>
                        <input type="text" name="judge" value="{{ $court_task->judge }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('judge')
                    </div>
                    <!-- 担当書記官 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当書記官</label>
                        <input type="text" name="clerk" value="{{ $court_task->clerk }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('clerk')
                    </div>
                    <!-- 電話（直通） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話（直通）</label>
                        <input type="text" name="tel_direct" value="{{ $court_task->tel_direct }}" 
                        placeholder="ハイフンなしで入力（例: 0312345678）" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('tel_direct')
                    </div>
                    <!-- FAX（直通） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">FAX（直通）</label>
                        <input type="text" name="fax_direct" value="{{ $court_task->fax_direct }}" 
                        placeholder="ハイフンなしで入力（例: 0312345678）" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('fax_direct')
                    </div>
                    <!-- メール（直通） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">メール（直通）</label>
                        <input type="email" name="email_direct" value="{{ $court_task->email_direct }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('email_direct')
                    </div>
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('court_task.show', $court_task->id) }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">裁判所対応削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの裁判所対応を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('court_task.destroy', $court_task->id) }}">
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
@endsection