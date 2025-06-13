@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">相談登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('consultation.store') }}" method="POST">
    @csrf

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">相談情報</div>

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
                <!-- 小見出し：クライアント -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    クライアント
                </div>
                <!-- 区分 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>区分
                    </label>
                    <select name="consultation_party" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.consultation_parties') as $key => $label)
                            <option value="{{ $key }}" @selected(old('consultation_party') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('consultation_party')
                </div>
                <!-- クライアントID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>クライアントID
                    </label>
                    <input type="text" name="client_id" value="{{ old('client_id') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('client_id')
                </div>

                <!-- 小見出し：相談 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    相談
                </div>
                <!-- 件名 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>件名
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('title')
                </div>
                <!-- 件名 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>問い合せ形態
                    </label>
                    <select name="inquirytype" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.inquirytypes') as $key => $label)
                            <option value="{{ $key }}" @selected(old('inquirytype') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('inquirytype')
                </div>
                <!-- 取扱事務所 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>取扱事務所
                    </label>
                    <select name="office_id" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.offices_id') as $key => $label)
                            <option value="{{ $key }}" @selected(old('office_id') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('office_id')
                </div>
                <!-- ステータス -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>ステータス
                    </label>
                    <select name="status" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.consultation_statuses') as $key => $label)
                            <option value="{{ $key }}" @selected(old('status') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('status')
                </div>
                <!-- 小見出し：関係者 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    相談
                </div>
            </div>
        </div>
        <!-- ボタン -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('consultation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                一覧に戻る
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                登録する
            </button>
        </div>
    </form>
</div>
@endsection