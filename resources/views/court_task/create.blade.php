@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">裁判所対応登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('court_task.store', ['business' => $business->id]) }}" method="POST">
    @csrf

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">裁判所対応情報</div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                        <ul class="list-disc pl-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <!-- 入力フィールド -->
            <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    基本情報
                </div>
                <!-- ステータス -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>ステータス
                    </label>
                    <select name="status" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.court_tasks_statuses') as $key => $label)
                            <option value="{{ $key }}" @selected(old('status') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('status')
                </div>
                <!-- ステータス詳細 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス詳細</label>
                    <input type="text" name="status_detail" value="{{ old('status_detail') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('status_detail')
                </div>
                <!-- タスク名 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>タスク名</label>
                    <input type="text" name="task_title" value="{{ old('task_title') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('task_title')
                </div>
                <!-- タスク分類 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>タスク分類
                    </label>
                        <select name="task_category" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.court_task_categories') as $key => $label)
                            <option value="{{ $key }}" @selected(old('task_category') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('task_category')
                </div>
                <!-- 事件番号 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">事件番号</label>
                    <input type="text" name="case_number" value="{{ old('case_number') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('case_number')
                </div>
                <!-- タスク内容 -->
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">タスク内容</label>
                    <textarea name="task_content" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('task_content') }}</textarea>
                    @errorText('task_content')
                </div>
                <!-- 担当弁護士 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        担当弁護士（名前で検索）
                    </label>
                    <select name="lawyer_id"
                            class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('lawyer_id') }}"
                            data-old-text="{{ old('lawyer_name_display') }}">
                            <option></option>
                    </select>
                    @errorText('lawyer_id')
                </div>
                <!-- 担当パラリーガル -->
                <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-1">
                        担当パラリーガル（名前で検索）
                    </label>
                    <select name="paralegal_id" class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('paralegal_id') }}"
                            data-old-text="{{ old('paralegal_name_display') }}">
                        <option></option>
                    </select>
                    @errorText('paralegal_id')
                </div>
                <!-- 期限 -->
                <div>
                    <label class="block font-semibold mb-1">期限：年月日</label>
                    <input type="date" name="deadline_date" value="{{ old('deadline_date') }}"
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
                                @endphp
                                <option value="{{ $time }}" {{ old('deadline_time') == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        @endfor
                    </select>
                    @errorText('deadline_time')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>受任案件：件名</label>
                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                    <input type="text"
                           value="{{ $business->title }}"
                           class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                           readonly>
                    @errorText('business_id')
                </div>
                <!-- 移動時間 -->
                <div>
                    <label class="block font-semibold mb-1">移動時間</label>
                    <select name="move_time" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 時間を選択 --</option>
                        @for ($h = 9; $h <= 20; $h++)
                            @foreach (['00', '15', '30', '45'] as $m)
                                @php
                                    $time = sprintf('%02d:%s', $h, $m);
                                @endphp
                                <option value="{{ $time }}" {{ old('move_time') == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        @endfor
                    </select>
                    @errorText('move_time')
                </div>
                <!-- メモ欄 -->
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">メモ欄</label>
                    <textarea name="memo" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('memo') }}</textarea>
                    @errorText('memo')
                </div>
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  裁判所情報
                </div>
                <!-- 裁判所マスタID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>裁判所</label>
                    <select name="court_id"
                        class="select-court w-full"
                        data-old-id="{{ old('court_id') }}"
                        data-old-text="{{ old('court_name_display') }}"> {{-- ←表示名（オプション） --}}
                        <option></option>
                    </select>
                    @errorText('court_id')
                </div>
                <!-- 担当係 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当係</label>
                    <input type="text" name="department" value="{{ old('department') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('department')
                </div>
                <!-- 担当裁判官 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当裁判官</label>
                    <input type="text" name="judge" value="{{ old('judge') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('judge')
                </div>
                <!-- 担当書記官 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当書記官</label>
                    <input type="text" name="clerk" value="{{ old('clerk') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('clerk')
                </div>
                <!-- 電話（直通） -->
                <div>
                    <label class="block font-semibold mb-1">電話（直通）</label>
                    <input type="text" name="tel_direct" value="{{ old('tel_direct') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('tel_direct')
                </div>
                <!-- FAX（直通） -->
                <div>
                    <label class="block font-semibold mb-1">FAX（直通）</label>
                    <input type="text" name="fax_direct" value="{{ old('fax_direct') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('fax_direct')
                </div>
                <!-- メール（直通） -->
                <div>
                    <label class="block font-semibold mb-1">メール（直通）</label>
                    <input type="email" name="email_direct" value="{{ old('email_direct') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('email_direct') 
                </div>
            </div>
        </div>
        <!-- ボタン -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('business.show', ['business' => $business->id]) }}#tab-courtTask"
               class="text-blue-600 hover:underline hover:text-blue-800">
                一覧に戻る
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                登録する
            </button>
        </div>
    </form>
</div>
@endsection