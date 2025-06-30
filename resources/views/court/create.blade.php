@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">裁判所マスタ登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <!-- ヘッダー -->
    <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">
        裁判所情報
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

        <form action="{{ route('court.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- 裁判所名 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>裁判所名
                    </label>
                        <input type="text" name="court_name" value="{{ old('court_name') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('court_name')
                </div>

                <!-- 裁判所区分 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>裁判所区分
                    </label>
                        <select name="court_type" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.court_types') as $key => $label)
                            <option value="{{ $key }}" @selected(old('court_type') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('court_type')
                </div>

                <!-- 郵便番号 -->
                <div>
                    <label class="block font-semibold mb-1">郵便番号</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('postal_code')
                </div>
                
                <!-- 所在地 -->
                <div>
                    <label class="block font-semibold mb-1">所在地</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                           placeholder="所在地を入力" class="w-full p-2 border rounded bg-white">
                    @errorText('location')
                </div>
                
                <!-- 電話番号 -->
                <div>
                    <label class="block font-semibold mb-1">電話番号</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                           placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number')
                </div>

                <!-- 備考 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">備考</label>
                    <textarea name="importantnotes" rows="4" maxlength="1000"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('importantnotes') }}</textarea>
                    @errorText('importantnotes')
                </div>
            </div>

            <!-- ボタン -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('court.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
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