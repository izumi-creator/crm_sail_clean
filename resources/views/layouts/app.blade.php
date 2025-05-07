<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CRMシステム') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        function toggleMenu() {
            let sidebar = document.getElementById("sidebar");
            let menuButton = document.getElementById("menuButton");

            let isClosed = sidebar.classList.toggle("closed");

            if (isClosed) {
                sidebar.style.width = "0px";
                menuButton.style.position = "fixed";
                menuButton.style.left = "15px";
                menuButton.style.right = "auto";
                menuButton.style.top = "20px";
                menuButton.style.zIndex = "9999";
                menuButton.style.visibility = "visible";
            } else {
                sidebar.style.width = "256px";
                menuButton.style.position = "absolute";
                menuButton.style.right = "10px";
                menuButton.style.left = "auto";
                menuButton.style.top = "50%";
                menuButton.style.transform = "translateY(-50%)";
            }

            menuButton.style.display = "block";
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
    <div class="w-full min-h-screen bg-gray-100 flex">
        <!-- サイドメニュー（sidebar.blade.php を読み込む） -->
        @include('layouts.sidebar')

        <div class="flex-1">
            <!-- メインコンテンツ -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @yield('scripts')
    <script>
        function resetPassword(userId) {
            const btn = document.getElementById('resetSubmitBtn');
            btn.disabled = true;
            btn.textContent = '実行中...';
        
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
            fetch(`/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('newPasswordArea').classList.remove('hidden');
                document.getElementById('newPasswordArea').textContent = data.newPassword;
                btn.style.display = 'none';
            })
            .catch(err => {
                alert('初期化に失敗しました');
                console.error(err);
            });
        }
        
        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
            // リセット状態に戻す（再度開けるように）
            document.getElementById('resetSubmitBtn').disabled = false;
            document.getElementById('resetSubmitBtn').textContent = '初期化する';
            document.getElementById('newPasswordArea').classList.add('hidden');
            document.getElementById('newPasswordArea').textContent = '';
        }
        </script>   
</body>
</html>