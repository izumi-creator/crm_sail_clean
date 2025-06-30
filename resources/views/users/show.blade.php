@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">スタッフ詳細</h2>

    <!-- スタッフ詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            @if (auth()->user()->role_type == 1)
                <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            @endif
            @if (auth()->user()->role_type == 1)
                <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
            @endif
        </div>

    <!-- ✅ スタッフ情報の見出し＋内容を枠で囲む -->
    <div class="border border-gray-300 overflow-hidden">

        <!-- 見出し -->
        <div class="bg-sky-700 text-white px-4 py-2 font-bold border">スタッフ情報</div>

        <!-- 内容 -->
        <div class="grid grid-cols-2 gap-6 p-4 text-sm">
        <!-- 氏名（2カラム使用） -->
       <div>
           <label class="block text-sm font-semibold text-gray-700 mb-1">
                氏名
          </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $user->name ?: '&nbsp;' !!}
             </div>
        </div>
            
        <!-- ユーザID -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                ユーザID（ログインID）
            </label>
                <div class="mt-1 p-2 border rounded bg-gray-50">
                    {!! $user->user_id ?: '&nbsp;' !!}
                </div>
        </div>
        <!-- 従業員区分 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                従業員区分
           </label>
                <div class="mt-1 p-2 border rounded bg-gray-50">
                    {!! config('master.employee_types')[$user->employee_type] ?? '&nbsp;' !!}
                </div>
        </div>
        <!-- 所属事務所 -->
        <div>
            <label class="font-bold">所属事務所</label>
                <div class="mt-1 p-2 border rounded bg-gray-50">
                    {!! config('master.offices')[$user->office_id] ?? '&nbsp;' !!}
           </div>
        </div>
        <!-- システム権限（左カラムだけに表示） -->
        <div>
             <label class="block text-sm font-semibold text-gray-700 mb-1">
                システム権限
            </label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! config('master.role_types')[$user->role_type] ?? '&nbsp;' !!}
              </div>
        </div>
        <!-- 右カラムは空 -->
         <div></div>
         <!-- 電話番号1 -->
        <div>
           <label class="font-bold">電話番号1</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $user->phone_number ?: '&nbsp;' !!}
                </div>
        </div>
        <!-- 電話番号2 -->
        <div>
            <label class="font-bold">電話番号2</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $user->phone_number2 ?: '&nbsp;' !!}
               </div>
        </div>
        <!-- メールアドレス1 -->
        <div>
           <label class="font-bold">メールアドレス1</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $user->email ?: '&nbsp;' !!}
              </div>
        </div>
        <!-- メールアドレス2 -->
        <div>
           <label class="font-bold">メールアドレス2</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
                   {!! $user->email2 ?: '&nbsp;' !!}
               </div>
        </div>
    </div>
    </div>
    <!-- ✅ パスワード関連ボタン（外枠の外に表示） -->
    <div class="relative mt-6 h-10">
       <!-- 左側：一覧に戻る -->
        <div class="absolute left-0">
            <a href="{{ route('users.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
        </div>
        
       <!-- パスワード変更（中央） -->
       @if (auth()->id() == $user->id)
       <div class="absolute left-1/2 transform -translate-x-1/2">
        <a href="{{ route('users.editPassword') }}?from={{ $user->id }}" class="bg-green-400 hover:bg-green-500 text-black px-4 py-2 rounded">
            パスワード変更
        </a>
        </div>
         @endif

       <!-- パスワード初期化（右） -->
       @if (auth()->user()->role_type == 1)
       <div class="absolute right-0">
            <button onclick="document.getElementById('resetModal').classList.remove('hidden')" class="bg-cyan-200 hover:bg-cyan-400 text-black px-4 py-2 rounded">パスワード初期化</button>
        </div>
        @endif
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded">
            <form method="POST" action="{{ route('users.update', $user->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">スタッフ編集</div>
            
                <!-- ✅ エラーボックスをgrid外に出す -->
                @if ($errors->any())
                <div class="p-6 pt-4 text-sm">
                    <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                        <ul class="list-disc pl-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- 入力フィールド -->
                <div class="grid grid-cols-2 gap-6 p-6 text-sm">
                    <!-- 氏名 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 氏名
                        </label>
                        <input type="text" name="name" value="{{ $user->name }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('name')
                    </div>

                    <!-- ユーザID -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> ユーザID（ログインID）：原則変更不可
                        </label>
                        <input type="text" name="user_id" value="{{ $user->user_id }}"
                                placeholder="8文字以上・英数字（例: user12345）"
                                class="w-full p-2 border rounded bg-white">
                        @errorText('user_id')
                    </div>
                
                    <!-- 従業員区分 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 従業員区分
                        </label>
                        <select name="employee_type" class="w-full p-2 border rounded bg-white">
                            @foreach (config('master.employee_types') as $key => $label)
                                <option value="{{ $key }}" @selected($user->employee_type == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('employee_type')
                    </div>
                
                    <!-- 所属事務所 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">所属事務所</label>
                        <select name="office_id" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.offices') as $key => $label)
                                <option value="{{ $key }}" @selected($user->office_id == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('office_id')
                    </div>
                
                    <!-- システム権限 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> システム権限
                        </label>
                        <select name="role_type" class="w-full p-2 border rounded bg-white">
                            @foreach (config('master.role_types') as $key => $label)
                                <option value="{{ $key }}" @selected($user->role_type == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('role_type')
                    </div>
                    <div></div>
                
                    <!-- 電話番号1 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号1</label>
                        <input type="text" name="phone_number" value="{{ $user->phone_number }}"
                               placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                        @errorText('phone_number')
                    </div>
                
                    <!-- 電話番号2 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号2</label>
                        <input type="text" name="phone_number2" value="{{ $user->phone_number2 }}"
                               placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                        @errorText('phone_number2')
                    </div>
                
                    <!-- メールアドレス1 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス1</label>
                        <input type="email" name="email" value="{{ $user->email }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('email')
                    </div>
                
                    <!-- メールアドレス2 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス2</label>
                        <input type="email" name="email2" value="{{ $user->email2 }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('email2')
                    </div>
                </div>
            
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('users.show', $user->id) }}"
                       class="px-4 py-2 bg-gray-300 text-black rounded min-w-[100px] text-center">
                       キャンセル
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 min-w-[100px]">
                        保存
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- 削除モーダル -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-md">
            <!-- ヘッダー -->
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">スタッフ削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこのスタッフを削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('users.destroy', $user->id) }}">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 text-black rounded">
                        キャンセル
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 min-w-[100px]">
                        削除
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- パスワード初期化モーダル -->
    <div id="resetModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-md">
            <!-- ヘッダー -->
            <div class="bg-cyan-500 text-white px-4 py-2 font-bold border-b">パスワード初期化</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm" id="resetModalBody">
                <p class="mb-2">このスタッフのパスワードを初期化しますか？</p>
                <p class="mb-4">初期化されたパスワードはスタッフ詳細画面の上部にメッセージとして表示されます。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('users.resetPassword', $user->id) }}">
                @csrf
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <button type="button" onclick="document.getElementById('resetModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 text-black rounded min-w-[100px]">
                        キャンセル
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-cyan-500 text-white rounded hover:bg-cyan-600 min-w-[100px]">
                        実行
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
@if ($errors->any())
<script>
    window.onload = function () {
        document.getElementById('editModal').classList.remove('hidden');
    };
</script>
@endif
@endsection