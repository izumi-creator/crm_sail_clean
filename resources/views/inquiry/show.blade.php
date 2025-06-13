@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">問合せ詳細</h2>

    <!-- 問合せ詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            @if (auth()->user()->role_type == 1)
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
            @endif
        </div>

    <!-- ✅ 問合せ情報の見出し＋内容を枠で囲む -->
    <div class="border border-gray-300 overflow-hidden">

        <!-- 見出し -->
        <div class="bg-sky-700 text-white px-4 py-2 font-bold border">問合せ情報</div>

        <!-- 内容 -->
        <div class="grid grid-cols-2 gap-6 p-4 text-sm">

        <!-- 問合せ日 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span>問合せ日</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {{ $inquiry->receptiondate ? $inquiry->receptiondate->format('Y-m-d H:i') : '―' }}
            </div>
        </div>
        <!-- ステータス -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span>ステータス</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->status ? config('master.inquiry_status')[$inquiry->status] : '&nbsp;' !!}
            </div>
        </div>
        <!-- 氏名（2カラム使用） -->
        <div class="col-span-2">
           <label class="block text-sm font-semibold text-gray-700 mb-1">
               <span class="text-red-500">*</span> お名前（漢字）
          </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->inquiries_name_kanji ?: '&nbsp;' !!}
             </div>
        </div>
        <div class="col-span-2">
           <label class="block text-sm font-semibold text-gray-700 mb-1">
               <span class="text-red-500">*</span> お名前（かな）
          </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->inquiries_name_kana ?: '&nbsp;' !!}
             </div>
        </div>
        <!-- 姓（漢字） -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span> 姓（漢字）
            </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->last_name_kanji ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- 名（漢字） -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span> 名（漢字）
            </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->first_name_kanji ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- 姓（かな） -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span> 姓（かな）
            </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->last_name_kana ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- 名（かな） -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">
                <span class="text-red-500">*</span> 名（かな）
            </label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->first_name_kana ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- 会社名 -->
        <div class="col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 会社名</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->corporate_name ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- 電話番号 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 電話番号</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->phone_number ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- メールアドレス -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> メールアドレス</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->email ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- 都道府県 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 都道府県</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->state ?: '&nbsp;' !!}
            </div>
        </div>
        <div></div>
        <!-- 第一希望日 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 第一希望日</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {{ $inquiry->firstchoice_datetime ? $inquiry->firstchoice_datetime->format('Y-m-d H:i') : '―' }}
            </div>
        </div>
        <!-- 第二希望日 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 第二希望日</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {{ $inquiry->secondchoice_datetime ? $inquiry->secondchoice_datetime->format('Y-m-d H:i') : '―' }}
            </div>
        </div>
        <!-- お問合せ内容 -->
        <div class="col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-1">お問合せ内容</label>
            <pre class="mt-1 p-2 border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $inquiry->inquirycontent }}</pre>
        </div>
        <!-- 流入経路 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 流入経路</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->route ? config('master.routes')[$inquiry->route] : '&nbsp;' !!}
            </div>
        </div>
        <!-- 流入経路詳細 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 流入経路詳細</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->routedetail ? config('master.routedetails')[$inquiry->routedetail] : '&nbsp;' !!}
            </div>
        </div>
        <!-- 1週間当たりの平均残業時間 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 1週間当たりの平均残業時間</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->averageovertimehoursperweek ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- 月収 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 月収</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->monthlyincome ?: '&nbsp;' !!}
            </div>
        </div>
        <!-- 勤続年数 -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 勤続年数</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->lengthofservice ?: '&nbsp;' !!}
            </div>
        </div>
        <div></div>
        <!-- 相談ID -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1"> 相談ID</label>
            <div class="mt-1 p-2 border rounded bg-gray-50">
                {!! $inquiry->consultation_id ?: '&nbsp;' !!}
            </div>
        </div>
    </div>
    </div>
    <!-- ✅ 外枠の外に表示 -->
    <div class="relative mt-6 h-10">
       <!-- 左側：一覧に戻る -->
        <div class="absolute left-0">
            <a href="{{ route('inquiry.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('inquiry.update', $inquiry->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">問合せ編集</div>

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
                    <!-- 問合せ日時 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>問合せ日時
                        </label>
                        <input type="datetime-local" name="receptiondate" value="{{ $inquiry->receptiondate }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('receptiondate')
                    </div>

                    <!-- ステータス -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>ステータス
                        </label>
                        <select name="status" class="w-full p-2 border rounded bg-white">
                            @foreach (config('master.inquiry_status') as $key => $label)
                                <option value="{{ $key }}" @selected($inquiry->status == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('status')
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>お名前（漢字） 
                        </label>
                            <input type="text" name="inquiries_name_kanji" value="{{ $inquiry->inquiries_name_kanji }}"
                               placeholder="姓・名の入力で自動反映"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-700 cursor-not-allowed" readonly>
                            @errorText('inquiries_name_kanji') 
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>お名前（かな）
                        </label>
                            <input type="text" name="inquiries_name_kana" value="{{ $inquiry->inquiries_name_kana }}"
                                placeholder="姓・名の入力で自動反映"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-700 cursor-not-allowed" readonly>
                            @errorText('inquiries_name_kana')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>姓（漢字）</label>
                        <input type="text" name="last_name_kanji" value="{{ $inquiry->last_name_kanji }}" 
                               placeholder="姓・名の入力で自動反映"
                               class="w-full p-2 border rounded bg-white">
                            @errorText('last_name_kanji')                    
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>名（漢字）</label>
                        <input type="text" name="first_name_kanji" value="{{ $inquiry->first_name_kanji }}" 
                               placeholder="姓・名の入力で自動反映"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('first_name_kanji')                    
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>姓（かな）</label>
                        <input type="text" name="last_name_kana" value="{{ $inquiry->last_name_kana }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('last_name_kana')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span>名（かな）</label>
                        <input type="text" name="first_name_kana" value="{{ $inquiry->first_name_kana }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('first_name_kana')
                    </div>

                    <!-- 会社名 -->
                    <div class="col-span-2">
                        <label class="block font-semibold mb-1">会社名</label>
                        <input type="text" name="corporate_name" value="{{ $inquiry->corporate_name }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('corporate_name')
                    </div>

                    <!-- 電話番号 -->
                    <div>
                        <label class="block font-semibold mb-1">電話番号</label>
                        <input type="text" name="phone_number" value="{{ $inquiry->phone_number }}"
                                placeholder="ハイフンなしで入力（例: 09012345678）"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('phone_number')
                    </div>
                    <!-- メールアドレス -->
                    <div>
                        <label class="block font-semibold mb-1">メールアドレス</label>
                        <input type="email" name="email" value="{{ $inquiry->email }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('email')
                    </div>
                    <!-- 都道府県 -->
                    <div>
                        <label class="block font-semibold mb-1">都道府県</label>
                        <select name="state" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 選択してください --</option>
                            @foreach (config('prefectures') as $prefecture)
                                <option value="{{ $prefecture }}" @selected($inquiry->state == $prefecture)>{{ $prefecture }}</option>
                            @endforeach
                        </select>                        
                        @errorText('state')
                    </div>
                    <div></div>
                    <!-- 第一希望 -->
                    <div>
                        <label class="block font-semibold mb-1">第一希望：年月日</label>
                        <input type="date" name="firstchoice_date"
                               value="{{ $inquiry->firstchoice_datetime ? $inquiry->firstchoice_datetime->format('Y-m-d') : '' }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('firstchoice_date')
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">第一希望：時間</label>
                        <select name="firstchoice_time" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 時間を選択 --</option>
                            @for ($h = 9; $h <= 20; $h++)
                                @foreach (['00', '15', '30', '45'] as $m)
                                    @php
                                        $time = sprintf('%02d:%s', $h, $m);
                                        $selected = $inquiry->firstchoice_datetime && $inquiry->firstchoice_datetime->format('H:i') === $time;
                                    @endphp
                                    <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>
                                        {{ $time }}
                                    </option>
                                @endforeach
                            @endfor
                        </select>
                        @errorText('firstchoice_time')
                    </div>
                    <!-- 第二希望 -->
                    <div>
                        <label class="block font-semibold mb-1">第二希望：年月日</label>
                        <input type="date" name="secondchoice_date"
                               value="{{ $inquiry->secondchoice_datetime ? $inquiry->secondchoice_datetime->format('Y-m-d') : '' }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('secondchoice_date')
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">第二希望：時間</label>
                        <select name="secondchoice_time" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 時間を選択 --</option>
                            @for ($h = 9; $h <= 20; $h++)
                                @foreach (['00', '15', '30', '45'] as $m)
                                    @php
                                        $time = sprintf('%02d:%s', $h, $m);
                                        $selected = $inquiry->secondchoice_datetime && $inquiry->secondchoice_datetime->format('H:i') === $time;
                                    @endphp
                                    <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>
                                        {{ $time }}
                                    </option>
                                @endforeach
                            @endfor
                        </select>
                        @errorText('secondchoice_time')
                    </div>
                    <!-- お問合せ内容 -->
                    <div class="col-span-2">
                        <label class="block font-semibold mb-1">お問合せ内容</label>
                        <textarea name="inquirycontent" rows="4" maxlength="1000"
                                  class="w-full p-2 border rounded bg-white resize-y">{{ $inquiry->inquirycontent }}</textarea>
                        @errorText('inquirycontent')
                    </div>
                    <!-- 流入経路 -->
                    <div>
                        <label class="block font-semibold mb-1">流入経路</label>
                        <select name="route" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.routes') as $key => $label)
                                <option value="{{ $key }}" @selected($inquiry->route == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('route')
                    </div>
                    <!-- 流入経路（詳細） -->
                    <div>
                        <label class="block font-semibold mb-1">流入経路（詳細）</label>
                        <select name="routedetail" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.routedetails') as $key => $label)
                                <option value="{{ $key }}" @selected($inquiry->routedetail == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('routedetail')
                    </div>
                    <!-- 1週間当たりの平均残業時間 -->
                    <div>
                        <label class="block font-semibold mb-1">1週間当たりの平均残業時間</label>
                        <input type="text" name="averageovertimehoursperweek" value="{{ $inquiry->averageovertimehoursperweek }}"
                            placeholder="〇〇時間（例: 10時間）"
                            class="w-full p-2 border rounded bg-white">
                        @errorText('averageovertimehoursperweek')
                    </div>
                    <!-- 月収 -->
                    <div>
                        <label class="block font-semibold mb-1">月収</label>
                        <input type="text" name="monthlyincome" value="{{ $inquiry->monthlyincome }}"
                                placeholder="〇〇万円（例: 30万円）"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('monthlyincome')
                    </div>
                    <!-- 勤続年数 -->
                    <div>
                        <label class="block font-semibold mb-1">勤続年数</label>
                        <input type="text" name="lengthofservice" value="{{ $inquiry->lengthofservice }}"
                                placeholder="〇〇年〇〇ヶ月（例: 5年10ヶ月）"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('lengthofservice')
                    </div>
                    <div></div>
                    <!-- 相談ID -->
                    <div>
                        <label class="block font-semibold mb-1">相談ID</label>
                        <input type="text" name="consultation_id" value="{{ $inquiry->consultation_id }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('consultation_id')
                    </div>
                </div>
                
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('inquiry.show', $inquiry->id) }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">問合せ削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの問合せを削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('inquiry.destroy', $inquiry->id) }}">
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
    window.addEventListener('load', function () {
        document.getElementById('editModal')?.classList.remove('hidden');
    });
</script>
@endif

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ▼ フルネーム補完
    const lastNameKanji = document.querySelector('input[name="last_name_kanji"]');
    const firstNameKanji = document.querySelector('input[name="first_name_kanji"]');
    const nameKanji = document.querySelector('input[name="inquiries_name_kanji"]');
    const lastNameKana = document.querySelector('input[name="last_name_kana"]');
    const firstNameKana = document.querySelector('input[name="first_name_kana"]');
    const nameKana = document.querySelector('input[name="inquiries_name_kana"]');

    function updateFullNameKanji() {
        if (!lastNameKanji || !firstNameKanji || !nameKanji) return;
        if (!lastNameKanji.value && !firstNameKanji.value) {
            nameKanji.value = '';
        } else {
            nameKanji.value = `${lastNameKanji.value}　${firstNameKanji.value}`.trim();
        }
    }

    function updateFullNameKana() {
        if (!lastNameKana || !firstNameKana || !nameKana) return;
        if (!lastNameKana.value && !firstNameKana.value) {
            nameKana.value = '';
        } else {
            nameKana.value = `${lastNameKana.value}　${firstNameKana.value}`.trim();
        }
    }

    if (nameKanji && nameKanji.value === '') updateFullNameKanji();
    if (nameKana && nameKana.value === '') updateFullNameKana();

    [lastNameKanji, firstNameKanji].forEach(el => el?.addEventListener('input', updateFullNameKanji));
    [lastNameKana, firstNameKana].forEach(el => el?.addEventListener('input', updateFullNameKana));
});
</script>
@endsection