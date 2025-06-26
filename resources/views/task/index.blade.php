@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full p-6"> <!-- ✅ ダッシュボードと統一 -->
        <h2 class="text-2xl font-bold mb-4 text-gray-800">タスク管理一覧</h2>

        <!-- 🔍 検索フォーム -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <form method="GET" action="{{ route('task.index') }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold">件名</label>
                        <input type="text" name="title" value="{{ request('title') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">worker名</label>
                        <input type="text" name="worker_name" value="{{ request('worker_name') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">期限日</label>
                        <input type="date" name="deadline_date" value="{{ request('deadline_date') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">ステータス</label>
                        <select name="status" class="w-full border px-3 py-2 rounded">
                            <option value="">選択してください</option>
                            @foreach (config('master.task_statuses') as $key => $label)
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
                    <a href="{{ route('task.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">検索条件クリア</a>
                    <a href="{{ route('task.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
                </div>
            </form>
        </div>

<!-- 📋 検索結果（ダッシュボードと同じ設定 + ページネーション内包） -->
<div class="mb-10">
    <h2 class="text-xl font-semibold mb-4">検索結果（{{ $tasks->total() }}件）</h2>
    <div class="p-4 border rounded-lg shadow bg-white">
        <div class="overflow-y-auto max-h-48"> <!-- ✅ 高さをダッシュボードと統一 -->
            <table class="w-full border-collapse border border-gray-300 table-fixed">
                <thead class="sticky top-0 bg-sky-700 text-white z-10 text-sm shadow-md border-b border-gray-300">
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
                    @foreach ($tasks as $task)
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
        </div>

        <!-- ✅ ページネーションを検索結果カード内に配置 -->
        <div class="mt-8 flex items-center space-x-4">
            <p class="text-gray-600">
                {{ __('pagination.showing', ['first' => $tasks->firstItem(), 'last' => $tasks->lastItem(), 'total' => $tasks->total()]) }}
            </p>
            {{ $tasks->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

@endsection