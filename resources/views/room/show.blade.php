@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">施設詳細</h2>

    <!-- 施設詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
        </div>

    <!-- ✅ 施設情報の見出し＋内容を枠で囲む -->
    <div class="border border-gray-300 overflow-hidden">

        <!-- 見出し -->
        <div class="bg-sky-700 text-white px-4 py-2 font-bold border">施設情報</div>

        <!-- 内容 -->
        <div class="grid grid-cols-2 gap-6 p-4 text-sm">
        <!-- 氏名（2カラム使用） -->
       <div>
           <label class="block text-sm font-semibold text-gray-700 mb-1">
               <span class="text-red-500">*</span> 施設名
          </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $room->room_name ?: '&nbsp;' !!}
             </div>
        </div>
            
        <!-- 場所 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span> 場所
            </label>
                <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! config('master.offices_id')[$room->office_id] ?? '&nbsp;' !!}
                </div>
        </div>
        <!-- GoogleカレンダーID -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span> GoogleカレンダーID
            </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $room->calendar_id ?: '&nbsp;' !!}
           </div>
        </div>
            <!-- 備考 -->
            <div class="col-span-2">
                <label class="font-bold">備考</label>
               <div class="mt-1 p-2 border rounded bg-gray-50">
               {!! $room->importantnotes ?: '&nbsp;' !!}
               </div>
        </div>
    </div>
    </div>
    <!-- ✅ 外枠の外に表示 -->
    <div class="relative mt-6 h-10">
       <!-- 左側：一覧に戻る -->
        <div class="absolute left-0">
            <a href="{{ route('room.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded">
            <form method="POST" action="{{ route('room.update', $room->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">施設編集</div>
            
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

                    <!-- 施設名 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 施設名
                        </label>
                        <input type="text" name="room_name" value="{{ $room->room_name }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('room_name')
                    </div>
                
                    <!-- 場所 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 場所
                        </label>
                        <select name="office_id" class="w-full p-2 border rounded bg-white">
                            @foreach (config('master.offices_id') as $key => $label)
                                <option value="{{ $key }}" @selected($room->office_id == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('office_id')
                    </div>
                                
                    <!-- GoogleカレンダーID -->
                    <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span> GoogleカレンダーID
                    </label>
                    <input type="text" name="calendar_id" value="{{ $room->calendar_id }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('calendar_id')
                    </div>

                    <!-- 備考 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">備考</label>
                        <input type="text" name="importantnotes" value="{{ $room->importantnotes }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">施設削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの施設を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('room.destroy', $room->id) }}">
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