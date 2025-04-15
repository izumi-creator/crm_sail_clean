@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">保険会社マスタ登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <!-- ヘッダー -->
    <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">
        保険会社情報
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

        <form action="{{ route('insurance.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- 保険会社名 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>保険会社名
                    </label>
                        <input type="text" name="insurance_name" value="{{ old('insurance_name') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('insurance_name')
                </div>
                <!-- 種類 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>保険会社区分
                    </label>
                        <select name="insurance_type" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.insurance_types') as $key => $label)
                            <option value="{{ $key }}" @selected(old('insurance_type') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('insurance_type')
                </div>
                <!-- 問合せ窓口１ -->
                <div>
                    <label class="block font-semibold mb-1">問合せ窓口１</label>
                    <input type="text" name="contactname" value="{{ old('contactname') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('contactname')
                </div>
                <div></div>
                <!-- 電話番号1 -->
                <div>
                    <label class="block font-semibold mb-1">電話番号1</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                           placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number')
                </div>
                <!-- メール1 -->
                <div>
                    <label class="block font-semibold mb-1">メールアドレス1</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('email')
                </div>
                <!-- 問合せ窓口２ -->
                <div>
                    <label class="block font-semibold mb-1">問合せ窓口2</label>
                    <input type="text" name="contactname2" value="{{ old('contactname2') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('contactname2')
                </div>
                <div></div>
                <!-- 電話番号2 -->
                <div>
                    <label class="block font-semibold mb-1">電話番号2</label>
                    <input type="text" name="phone_number2" value="{{ old('phone_number2') }}"
                           placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number2')
                </div>
                <!-- メール1 -->
                <div>
                    <label class="block font-semibold mb-1">メールアドレス2</label>
                    <input type="email" name="email2" value="{{ old('email2') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('email2')
                </div>
                <!-- 備考 -->
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">備考</label>
                    <input type="text" name="importantnotes" value="{{ old('importantnotes') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('importantnotes')
                </div>
            </div>

            <!-- ボタン -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('insurance.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
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