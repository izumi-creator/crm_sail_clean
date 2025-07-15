@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">スタッフ登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <!-- ヘッダー -->
    <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">
        スタッフ情報
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

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- ユーザID -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>ユーザID
                    </label>
                        <input type="text" name="user_id" value="{{ old('user_id') }}"
                            placeholder="8文字以上・英数字（例: user12345）"
                            class="w-full p-2 border rounded bg-white">
                    @errorText('user_id')
                </div>

                <!-- 氏名 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>氏名
                    </label>
                        <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('name')
                </div>

                <!-- 従業員区分 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>従業員区分
                    </label>
                        <select name="employee_type" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.employee_types') as $key => $label)
                            <option value="{{ $key }}" @selected(old('employee_type') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('employee_type')
                </div>

                <!-- 所属事務所 -->
                <div>
                    <label class="block font-semibold mb-1">所属事務所</label>
                    <select name="office_id" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.offices') as $key => $label)
                            <option value="{{ $key }}" @selected(old('office_id') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('office_id')
                </div>

                <!-- システム権限 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>システム権限
                    </label>
                        <select name="role_type" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.role_types') as $key => $label)
                            <option value="{{ $key }}" @selected(old('role_type') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('role_type')
                </div>

                <!-- システム利用区分 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>システム利用区分
                    </label>
                        <select name="user_status" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.user_statuses') as $key => $label)
                            <option value="{{ $key }}" @selected(old('user_status') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('user_status')
                </div>                

                <!-- 電話番号 -->
                <div>
                    <label class="block font-semibold mb-1">電話番号1</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                           placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number')
                </div>
                <div>
                    <label class="block font-semibold mb-1">電話番号2</label>
                    <input type="text" name="phone_number2" value="{{ old('phone_number2') }}"
                           placeholder="ハイフンなしで入力（例: 09012345678）" class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number2')
                </div>

                <!-- メール -->
                <div>
                    <label class="block font-semibold mb-1">メールアドレス1</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('email')
                </div>
                <div>
                    <label class="block font-semibold mb-1">メールアドレス2</label>
                    <input type="email" name="email2" value="{{ old('email2') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('email2')
                </div>

                <!-- SlackチャンネルID -->
                <div>
                    <label class="block font-semibold mb-1">SlackチャンネルID</label>
                    <input type="text" name="slack_channel_id" value="{{ old('slack_channel_id') }}"
                           placeholder="SlackチャンネルのIDを入力（例: C01ABCD2EFG）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('slack_channel_id')
                </div>
                <div></div>

                <!-- パスワード -->
                <div>
                    <label class="block font-semibold mb-1">パスワード<span class="text-red-500">*</span></label>
                    <input type="password" name="password"
                        placeholder="8文字以上・英大文字/小文字/数字/記号のうち3種以上"
                        class="w-full p-2 border rounded bg-white">
                    @errorText('password')
                </div>
                <div>
                    <label class="block font-semibold mb-1">パスワード確認<span class="text-red-500">*</span></label>
                    <input type="password" name="password_confirmation" class="w-full p-2 border rounded bg-white">
                    @errorText('password_confirmation')
                </div>
            </div>

            <!-- ボタン -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('users.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
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