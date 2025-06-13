<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>

    <!-- ✅ Tailwind CDN を追加 -->
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="flex items-center justify-center h-screen bg-blue-100">
    
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-lg">
        <!-- ロゴ -->
        <div class="flex justify-center">
            <img src="{{ asset('images/logo_new2.png') }}" alt="ロゴ" class="w-[22rem] max-w-md">
        </div>

        <!-- ログインフォーム -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- ユーザーID -->
            <div>
                <label for="user_id" class="sr-only">ユーザーID</label>
                <input id="user_id" type="text" name="user_id"
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500"
                       placeholder="ユーザーID" required autofocus>
            </div>

            <!-- パスワード -->
            <div class="mt-4">
                <label for="password" class="sr-only">パスワード</label>
                <input id="password" type="password" name="password"
                       class="w-full px-4 py-2 mt-1 border border-gray-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500"
                       placeholder="パスワード" required>
            </div>

            <!-- ログインボタン -->
            <div class="mt-6">
                <button type="submit" 
                        class="w-full px-4 py-3 text-white bg-sky-700 rounded-md hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                    ログイン
                </button>
            </div>

            <!-- パスワードリセットリンク -->
            <div class="mt-4 text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:underline">
                    パスワードをお忘れの場合は管理者へお問合せください
                </a>
            </div>
        </form>
    </div>

</body>
</html>