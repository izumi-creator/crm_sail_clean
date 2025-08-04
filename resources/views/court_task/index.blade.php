@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full p-6"> <!-- ✅ ダッシュボードと統一 -->
        <h2 class="text-2xl font-bold mb-4 text-gray-800">裁判所対応一覧</h2>

        <!-- 🔍 検索フォーム -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <form method="GET" action="{{ route('court_task.index') }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold">タスク名</label>
                        <input type="text" name="task_title" value="{{ request('task_title') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">裁判所名</label>
                        <input type="text" name="court_name" value="{{ request('court_name') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">事件番号</label>
                        <input type="text" name="case_number" value="{{ request('case_number') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">ステータス</label>
                        <select name="status" class="w-full border px-3 py-2 rounded">
                            <option value="">選択してください</option>
                            @foreach (config('master.court_tasks_statuses') as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == (string)$key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <!-- ボタンエリア -->
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded min-w-[100px]">検索</button>
                    <a href="{{ route('court_task.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">検索条件クリア</a>
                </div>
            </form>
        </div>

<!-- 📋 検索結果（ダッシュボードと同じ設定 + ページネーション内包） -->
<div class="mb-10">
    <h2 class="text-xl font-semibold mb-4">検索結果（{{ $court_tasks->total() }}件）</h2>
    <div class="p-4 border rounded-lg shadow bg-white">
        <div class="overflow-y-auto max-h-48"> <!-- ✅ 高さをダッシュボードと統一 -->
            <table class="w-full border-collapse border border-gray-300 table-fixed">
                <thead class="sticky top-0 bg-sky-700 text-white z-10 text-sm shadow-md border-b border-gray-300">
                    <tr>
                        <th class="border p-2 w-[6%]">ID</th>
                        <th class="border p-2 w-[15%]">受任案件：件名</th>
                        <th class="border p-2 w-[15%]">タスク名</th>
                        <th class="border p-2 w-[10%]">タスク分類</th>
                        <th class="border p-2 w-[10%]">裁判所名</th>
                        <th class="border p-2 w-[10%]">担当弁護士</th>
                        <th class="border p-2 w-[10%]">担当パラリーガル</th>
                        <th class="border p-2 w-[10%]">期限</th>
                        <th class="border p-2 w-[10%]">ステータス</th>
                        <th class="border p-2 w-[14%]">事件番号</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($court_tasks as $courtTask)
                    <tr>
                        <td class="border px-2 py-[6px] truncate">{{ $courtTask->id }}</td>
                        <td class="border px-2 py-[6px] truncate">
                            <a href="{{ route('business.show', $courtTask->business_id) }}" class="text-blue-500">
                                {{ $courtTask->business->title ?? '未設定' }}
                            </a>
                        <td class="border px-2 py-[6px] truncate">
                            <a href="{{ route('court_task.show', $courtTask->id) }}" class="text-blue-500">
                                {{ $courtTask->task_title }}
                            </a>
                        </td>
                        <td class="border px-2 py-[6px] truncate">
                            {{ config('master.court_task_categories')[(string)$courtTask->task_category] ?? '未設定' }}
                        </td>
                        <td class="border px-2 py-[6px] truncate">
                            {!! optional($courtTask->court)->court_name ?: '&nbsp;' !!}
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
                        <td class="border px-2 py-[6px] truncate">
                            {!! $courtTask->case_number ?: '&nbsp;' !!}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ✅ ページネーションを検索結果カード内に配置 -->
        <div class="mt-8 flex items-center space-x-4">
            <p class="text-gray-600">
                {{ __('pagination.showing', ['first' => $court_tasks->firstItem(), 'last' => $court_tasks->lastItem(), 'total' => $court_tasks->total()]) }}
            </p>
            {{ $court_tasks->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

@endsection