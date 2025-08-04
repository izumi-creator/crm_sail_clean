@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">施設管理登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <!-- ヘッダー -->
    <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">
        施設情報
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

        <form action="{{ route('room.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- 施設名 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>施設名
                    </label>
                        <input type="text" name="room_name" value="{{ old('room_name') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('room_name')
                </div>

                <!-- 場所 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>場所
                    </label>
                        <select name="office_id" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.offices_id') as $key => $label)
                            <option value="{{ $key }}" @selected(old('office_id') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('office_id')
                </div>

                <!-- GoogleカレンダーID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>GoogleカレンダーID
                    </label>
                    <input type="text" name="calendar_id" value="{{ old('calendar_id') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('calendar_id')
                </div>
                
                <!-- 備考 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">備考</label>
                    <textarea name="importantnotes" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('importantnotes') }}</textarea>
                    @errorText('importantnotes')
                </div>
            </div>

            <!-- ボタン -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('room.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                    一覧に戻る
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    登録する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection