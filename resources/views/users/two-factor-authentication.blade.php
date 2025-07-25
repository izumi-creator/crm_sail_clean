@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen px-4 py-8">
    <div class="w-full max-w-xl bg-white p-8 rounded shadow-md">

        {{-- 🔔 警告メッセージ（リダイレクト時） --}}
        @if (session('warning'))
            <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded text-center font-semibold">
                {{ session('warning') }}
            </div>
        @endif

        <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">二要素認証（2FA）設定</h2>

        @if (session('status') === 'two-factor-authentication-enabled')
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                二要素認証を有効にしました。
            </div>
        @elseif (session('status') === 'two-factor-authentication-confirmed')
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                認証に成功しました。ログイン準備完了です！
            </div>
        @endif

        @if (! auth()->user()->two_factor_secret)
            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                @csrf
                <button type="submit" class="w-full bg-sky-700 hover:bg-sky-800 text-white font-bold py-2 px-4 rounded">
                    二要素認証を有効にする
                </button>
            </form>
        @else
            <p class="mb-2 text-gray-700 text-sm">
                以下のQRコードをGoogle Authenticatorアプリなどで読み取ってください：
            </p>

            <div class="mb-4 flex justify-center">
                {!! auth()->user()->twoFactorQrCodeSvg() !!}
            </div>

            <form method="POST" action="{{ url('/user/confirmed-two-factor-authentication') }}">
                @csrf
                <label for="code" class="block text-sm font-medium text-gray-700">認証コード（6桁）</label>
                <input type="text" name="code" id="code" required
                       class="w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 mt-1 focus:ring focus:ring-blue-300">

                <button type="submit"
                        class="mt-4 w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    認証して有効化
                </button>
            </form>
        @endif
    </div>
</div>
@endsection