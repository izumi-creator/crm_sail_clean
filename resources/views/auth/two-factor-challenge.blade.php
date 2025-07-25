<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2要素認証</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<div class="flex flex-col items-center justify-center min-h-screen bg-blue-100">
    <div class="w-full max-w-md bg-white p-8 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">二要素認証</h2>
        <p class="text-sm text-gray-600 text-center mb-6">
            Google Authenticatorに表示された6桁コードを入力してください
        </p>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <strong>エラー:</strong> {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.login') }}">
            @csrf

            <!-- 6桁コード -->
            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700">6桁の認証コード</label>
                <input id="code" name="code" type="text" inputmode="numeric" maxlength="6"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-400"
                    placeholder="例：123456" required />
            </div>

            <!-- ※リカバリーコード欄は今後削除可能（暫定でコメントアウト） -->
            {{-- 
            <div class="mb-6">
                <label for="recovery_code" class="block text-sm font-medium text-gray-700">リカバリーコード</label>
                <input id="recovery_code" name="recovery_code" type="text"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm px-4 py-2 focus:ring-2 focus:ring-blue-400"
                    placeholder="xxxx-xxxx-xxxx" />
            </div>
            --}}

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-sky-700 hover:bg-sky-800 text-white font-bold py-2 px-6 rounded focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                    認証してログイン
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>