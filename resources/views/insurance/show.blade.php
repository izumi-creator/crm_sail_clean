@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">保険会社詳細</h2>

    <!-- 保険会社詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
        </div>

    <!-- ✅ 保険会社情報の見出し＋内容を枠で囲む -->
    <div class="border border-gray-300 overflow-hidden">

        <!-- 見出し -->
        <div class="bg-sky-700 text-white px-4 py-2 font-bold border">保険会社情報</div>

        <!-- 内容 -->
        <div class="grid grid-cols-2 gap-6 p-4 text-sm">
        <!-- 氏名（2カラム使用） -->
       <div>
           <label class="block text-sm font-semibold text-gray-700 mb-1">
               <span class="text-red-500">*</span> 保険会社名
          </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $insurance->insurance_name ?: '&nbsp;' !!}
             </div>
        </div>
            
        <!-- 保険会社種類 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span> 保険会社区分
            </label>
                <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! config('master.insurance_types')[$insurance->insurance_type] ?? '&nbsp;' !!}
                </div>
        </div>
        <!-- 問合せ窓口１ -->
        <div>
            <label class="font-bold">問合せ窓口１</label>
                <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $insurance->contactname ?: '&nbsp;' !!}
           </div>
        </div>
        <div></div>
        <!-- 電話番号1 -->
        <div>
             <label class="block text-sm font-semibold text-gray-700 mb-1">
                <label class="font-bold">電話番号1</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
               {!! $insurance->phone_number ?: '&nbsp;' !!}
               </div>
        </div>
        <!-- メールアドレス1 -->
        <div>
            <label class="font-bold">メールアドレス1</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
               {!! $insurance->email ?: '&nbsp;' !!}
               </div>
        </div>
                <!-- 問合せ窓口2 -->
                <div>
            <label class="font-bold">問合せ窓口2</label>
                <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $insurance->contactname2 ?: '&nbsp;' !!}
           </div>
        </div>
        <div></div>
        <!-- 電話番号2 -->
        <div>
            <label class="font-bold">電話番号2</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
               {!! $insurance->phone_number2 ?: '&nbsp;' !!}
               </div>
        </div>
        <!-- メールアドレス2 -->
        <div>
            <label class="font-bold">メールアドレス2</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
               {!! $insurance->email2 ?: '&nbsp;' !!}
               </div>
        </div>
            <!-- 備考 -->
            <div class="col-span-2">
                <label class="font-bold">備考</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
               {!! $insurance->importantnotes ?: '&nbsp;' !!}
               </div>
        </div>
    </div>
    </div>
    <!-- ✅ 外枠の外に表示 -->
    <div class="relative mt-6 h-10">
       <!-- 左側：一覧に戻る -->
        <div class="absolute left-0">
            <a href="{{ route('insurance.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded">
            <form method="POST" action="{{ route('insurance.update', $insurance->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">保険会社編集</div>
            
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
                        <input type="text" name="insurance_name" value="{{ $insurance->insurance_name }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('insurance_name')
                    </div>
                
                    <!-- 保険会社種類 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 種別
                        </label>
                        <select name="insurance_type" class="w-full p-2 border rounded bg-white">
                            @foreach (config('master.insurance_types') as $key => $label)
                                <option value="{{ $key }}" @selected($insurance->insurance_type == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('insurance_type')
                    </div>
                                
                    <!-- 問合せ窓口１ -->
                    <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">問合せ窓口１</label>
                    <input type="text" name="contactname" value="{{ $insurance->contactname }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('contactname')
                    </div>
                    <div></div>

                    <!-- 電話番号1 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号1</label>
                        <input type="text" name="phone_number" value="{{ $insurance->phone_number }}"
                               placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                        @errorText('phone_number')
                    </div>
                
                    <!-- メールアドレス1 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス1</label>
                        <input type="email" name="email" value="{{ $insurance->email }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('email')
                    </div>

                    <!-- 問合せ窓口2 -->
                    <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">問合せ窓口2</label>
                    <input type="text" name="contactname2" value="{{ $insurance->contactname2 }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('contactname2')
                    </div>
                    <div></div>

                    <!-- 電話番号1 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号2</label>
                        <input type="text" name="phone_number2" value="{{ $insurance->phone_number2 }}"
                               placeholder="ハイフンなしで入力（例: 0312345678）" class="w-full p-2 border rounded bg-white">
                        @errorText('phone_number2')
                    </div>
                
                    <!-- メールアドレス2 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">メールアドレス2</label>
                        <input type="email" name="email2" value="{{ $insurance->email2 }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('email2')
                    </div>

                    <!-- 備考 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">備考</label>
                        <input type="text" name="importantnotes" value="{{ $insurance->importantnotes }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('importantnotes')
                    </div>
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 text-black rounded min-w-[100px]">
                        キャンセル
                    </button>
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">保険会社削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの保険会社を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('insurance.destroy', $insurance->id) }}">
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