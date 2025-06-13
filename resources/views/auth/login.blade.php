<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>    
    <!-- ✅ Tailwind CDN を追加 -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<div class="flex flex-col items-center justify-center min-h-screen bg-blue-100">
    <div class="w-full max-w-md text-center">
        <!-- ロゴ -->
        <img src="{{ asset('images/logo_new2.png') }}" alt="ロゴ" 
             class="w-full h-auto mx-auto">

        <!-- エラーメッセージ表示 -->
        @if ($errors->any())
        <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <strong>エラー:</strong> {{ $errors->first() }}
        </div>
        @endif
        
        <!-- フォーム -->
        <form method="POST" action="{{ route('login') }}" class="mt-6">
            @csrf

            <!-- ユーザーID -->
            <div>
                <label for="user_id" class="sr-only">ユーザーID</label>
                <input id="user_id" type="text" name="user_id" required autofocus 
                       placeholder="ユーザーID" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300">
            </div>

            <!-- パスワード -->
            <div class="mt-4">
                <label for="password" class="sr-only">パスワード</label>
                <input id="password" type="password" name="password" required 
                       placeholder="パスワード"
                       class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-300">
            </div>

            <!-- ログインボタン -->
            <div class="mt-6">
                <button type="submit" 
                    class="w-full px-4 py-3 text-white !bg-sky-700 rounded-md hover:!bg-sky-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                    ログイン
                </button>
            </div>

            <!-- パスワードリセットリンク -->
            <div class="mt-4 text-center">
                パスワードをお忘れの場合は管理者へお問合せください
            </div>
        </form>
    </div>
</div>

</body>
</html>
