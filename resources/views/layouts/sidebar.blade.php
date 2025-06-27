<!-- サイドメニュー -->
<aside id="sidebar" class="w-64 bg-gray-900 text-white min-h-screen relative flex flex-col transition-all duration-300">
    <!-- ロゴエリア -->
    <div class="w-full h-20 bg-gray-800 flex items-center justify-center overflow-hidden">
        <img src="/images/logo_new.png" alt="CRM ロゴ" class="w-full h-full object-cover">
     </div>
    <!-- ログインユーザ名エリア（エリア全体がクリック可能） -->
    <a href="{{ route('users.show', Auth::user()->id) }}" class="block">
        <div class="p-4 text-center border-b border-gray-700 flex items-center justify-start hover:bg-gray-700 rounded">
            <i class="fas fa-user text-gray-400 text-xl mr-6"></i>
            <div class="text-left leading-tight">
                <div class="text-base text-white">
                    {{ Auth::user()->name }}
                </div>
                <div class="text-xs text-gray-400">
                    ユーザID: {{ Auth::user()->user_id }}
                </div>
            </div>
        </div>
    </a>
    <!-- メニューエリア -->
    <div class="p-4 border-b border-gray-700 relative flex justify-between items-center">
        <p class="text-base font-bold text-gray-300">メニュー</p>
        <button id="menuButton" onclick="toggleMenu()" 
            class="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-gray-900 text-white rounded z-50 transition-all duration-300">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <nav class="p-4 flex-grow">
        <ul class="mt-2">
            <li class="mb-1"><a href="{{ route('dashboard') }}" class="block p-1 text-base hover:bg-gray-700 rounded">ダッシュボード</a></li>
            <li class="mb-1"><a href="{{ route('client.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">クライアント</a></li>
            <li class="mb-1"><a href="{{ route('inquiry.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">問合せ</a></li>
            <li class="mb-1"><a href="{{ route('consultation.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">相談</a></li>
            <li class="mb-1"><a href="{{ route('business.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">受任案件</a></li>
            <li class="mb-1"><a href="{{ route('advisory.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">顧問契約</a></li>
            <li class="mb-1"><a href="{{ route('advisory_consultation.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">顧問相談</a></li>
            <li class="mb-1"><a href="{{ route('relatedparty.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">関係者</a></li>
            <li class="mb-1"><a href="{{ route('task.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">タスク</a></li>
            <li class="mb-1"><a href="{{ route('negotiation.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">折衝履歴</a></li>
            <li class="mb-1"><a href="{{ route('accountancy.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">会計</a></li>
            <li class="mb-1"><a href="{{ route('court.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">裁判所マスタ</a></li>
            <li class="mb-1"><a href="{{ route('insurance.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">保険会社マスタ</a></li>
            <li class="mb-1"><a href="{{ route('users.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">スタッフ</a></li>
            <li class="mb-1"><a href="{{ route('room.index') }}" class="block p-1 text-base hover:bg-gray-700 rounded">施設</a></li>
            <li class="mb-1"><a href="{{ route('export.download') }}" class="block p-1 text-base hover:bg-gray-700 rounded">データダウンロード</a></li>
            <li class="mb-1"><a href="{{ route('users.editPassword') }}" class="block p-1 text-base hover:bg-gray-700 rounded">パスワード変更</a></li>
            <li class="mb-1"><a href="{{ route('consultation.create') }}" class="block p-1 text-base hover:bg-gray-700 rounded">★相談登録★</a></li>
        </ul>
    </nav>
    <!-- ログアウトエリア（サイドメニュー最下部） -->
    <div class="p-4 border-t border-gray-700 mt-auto">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left block p-0 text-base text-white hover:text-red-500 rounded">
                <i class="fas fa-sign-out-alt mr-6"></i>ログアウト
            </button>
        </form>
    </div>

</aside>