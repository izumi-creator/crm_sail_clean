<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRMシステム</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function toggleMenu() {
            let sidebar = document.getElementById("sidebar");
            let menuButton = document.getElementById("menuButton");

            if (sidebar.classList.contains("closed")) {
                // 🔥 メニューを開く
                sidebar.classList.remove("closed");
                sidebar.style.width = "256px";
                menuButton.style.position = "absolute";
                menuButton.style.right = "10px";  // メニュー文字の右端
                menuButton.style.left = "auto";
                menuButton.style.display = "block";  // 🔥 絶対に消えないようにする！
            } else {
                // 🔥 メニューを閉じる
                sidebar.classList.add("closed");
                sidebar.style.width = "0px";
                menuButton.style.position = "fixed";
                menuButton.style.left = "10px";  // 左上端
                menuButton.style.right = "auto";
                menuButton.style.display = "block";  // 🔥 絶対に消えないようにする！
            }
        }
    </script>
    <style>
        #sidebar.closed {
            width: 0;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-200 flex">

    <!-- サイドメニュー -->
    <aside id="sidebar" class="w-64 bg-gray-900 text-white min-h-screen relative flex flex-col transition-all duration-300">
        <!-- ロゴエリア -->
        <div class="w-full h-20 bg-gray-800 flex items-center justify-center overflow-hidden">
            <img src="/images/logo_new.png" alt="CRM ロゴ" class="w-full h-full object-cover">
        </div>
        <!-- ログインユーザ名エリア（表示確認のため静的に「テスト 太郎」表示） -->
        <div class="p-4 text-center border-b border-gray-700">
            <p class="text-lg font-semibold text-gray-300">テスト 太郎</p>
        </div>
        <!-- メニューエリア -->
        <div class="p-4 border-b border-gray-700 relative flex justify-between items-center">
            <p class="text-lg font-bold text-gray-300">メニュー</p>
            <!-- ボタンの位置をメニュー文字の右端に固定 -->
            <button id="menuButton" onclick="toggleMenu()" class="absolute right-4 top-0 p-2 bg-gray-900 text-white rounded z-50">
                ☰
            </button>
        </div>
        <nav class="p-4 flex-grow">
            <ul class="mt-2">
                <li class="mb-2"><a href="{{ route('dashboard') }}" class="block p-2 text-lg hover:bg-gray-700 rounded">ダッシュボード</a></li>
                <li class="mb-2"><a href="{{ route('customers.index') }}" class="block p-2 text-lg hover:bg-gray-700 rounded">顧客管理</a></li>
                <li class="mb-2"><a href="{{ route('projects.index') }}" class="block p-2 text-lg hover:bg-gray-700 rounded">案件管理</a></li>
                <li class="mb-2"><a href="#" class="block p-2 text-lg hover:bg-gray-700 rounded">パスワード変更</a></li>
            </ul>
        </nav>
        <!-- ログアウトエリア（サイドメニュー最下部） -->
        <div class="p-4 border-t border-gray-700 mt-auto">
            <a href="#" class="block p-2 text-lg text-white hover:text-red-500 rounded">ログアウト</a>
        </div>
    </aside>

    <!-- メインコンテンツ -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</body>
</html>
