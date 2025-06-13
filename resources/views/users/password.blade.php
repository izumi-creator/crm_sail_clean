@extends('layouts.app')

@section('content')
<!-- 外枠カード（白背景 + 丸みあり） -->
<div class="max-w-5xl mx-auto mt-10 bg-white shadow-lg p-8 rounded-lg">
    <!-- ヘッダー -->
    <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">
        パスワード変更
    </div>

    <!-- フォーム全体（囲み）※丸みなし -->
    <div class="p-6 border border-gray-300 border-t-0 text-sm">
        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                <ul class="list-disc pl-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('users.updatePassword') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-1 font-semibold" for="current_password">現在のパスワード</label>
                <input type="password" name="current_password" id="current_password"
                       class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="mb-4">
                <label class="block mb-1 font-semibold" for="new_password">新しいパスワード</label>
                <input type="password" name="new_password" id="new_password"
                    placeholder="8文字以上・英大文字/小文字/数字/記号のうち3種以上"
                    class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <div class="mb-6">
                <label class="block mb-1 font-semibold" for="new_password_confirmation">新しいパスワード（確認）</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                       class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring focus:border-blue-300">
            </div>

            <!-- ボタンエリア -->
            <div class="flex justify-end space-x-2">
                <!-- キャンセルボタン -->
                <a href="{{ route('users.show', auth()->id()) }}"
                   class="px-4 py-2 bg-gray-300 text-black rounded hover:bg-gray-400">
                    キャンセル
                </a>

                <!-- 保存ボタン -->
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    変更する
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
