@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">問合せ登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <!-- ヘッダー -->
    <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">
        問合せ情報
    </div>

    <!-- 入力フィールド -->
    <div class="p-6 border border-gray-300 border-t-0 text-sm">
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                <ul class="list-disc pl-6">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inquiry.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-2 gap-6">
                <!-- 問合せ日時 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>問合せ日時
                    </label>
                    <input type="datetime-local" name="receptiondate" value="{{ old('receptiondate') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('receptiondate')
                </div>

                <!-- ステータス -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>ステータス
                    </label>
                    <select name="status" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.inquiry_status') as $key => $label)
                            <option value="{{ $key }}" @selected(old('status') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('status')
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>お名前（漢字） 
                    </label>
                        <input type="text" name="inquiries_name_kanji" value="{{ old('inquiries_name_kanji') }}"
                           placeholder="姓・名の入力で自動反映"
                           class="w-full p-2 border rounded bg-gray-100 text-gray-700 cursor-not-allowed" readonly>
                        @errorText('inquiries_name_kanji') 
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>お名前（かな）
                    </label>
                        <input type="text" name="inquiries_name_kana" value="{{ old('inquiries_name_kana') }}"
                            placeholder="姓・名の入力で自動反映"
                           class="w-full p-2 border rounded bg-gray-100 text-gray-700 cursor-not-allowed" readonly>
                        @errorText('inquiries_name_kana')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>姓（漢字）</label>
                    <input type="text" name="last_name_kanji" value="{{ old('last_name_kanji') }}" 
                        class="w-full p-2 border rounded bg-white">
                        @errorText('last_name_kanji')                    
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>名（漢字）</label>
                    <input type="text" name="first_name_kanji" value="{{ old('first_name_kanji') }}" 
                        class="w-full p-2 border rounded bg-white">
                    @errorText('first_name_kanji')                    
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>姓（かな）</label>
                    <input type="text" name="last_name_kana" value="{{ old('last_name_kana') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('last_name_kana')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>名（かな）</label>
                    <input type="text" name="first_name_kana" value="{{ old('first_name_kana') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('first_name_kana')
                </div>

                <!-- 会社名 -->
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">会社名</label>
                    <input type="text" name="corporate_name" value="{{ old('corporate_name') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('corporate_name')
                </div>

                <!-- 電話番号 -->
                <div>
                    <label class="block font-semibold mb-1">電話番号</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                            placeholder="ハイフンなしで入力（例: 09012345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number')
                </div>
                <!-- メールアドレス -->
                <div>
                    <label class="block font-semibold mb-1">メールアドレス</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('email') 
                </div>
                <!-- 都道府県 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">都道府県</label>
                    <select name="state" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 選択してください --</option>
                        @foreach (config('prefectures') as $prefecture)
                            <option value="{{ $prefecture }}" {{ old('state', $inquiry->state ?? '') == $prefecture ? 'selected' : '' }}>
                                {{ $prefecture }}
                            </option>
                        @endforeach
                    </select>
                    @errorText('state')
                </div>
                <div></div>
                <!-- 第一希望日 -->
                <div>
                    <label class="block font-semibold mb-1">第一希望：年月日</label>
                    <input type="date" name="firstchoice_date" value="{{ old('firstchoice_date') }}"
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
                                @endphp
                                <option value="{{ $time }}" {{ old('firstchoice_time') == $time ? 'selected' : '' }}>
                                    {{ $time }}
                                </option>
                            @endforeach
                        @endfor
                    </select>
                    @errorText('firstchoice_time')
                </div>
                <!-- 第二希望日 -->
                <div>
                    <label class="block font-semibold mb-1">第二希望：年月日</label>
                    <input type="date" name="secondchoice_date" value="{{ old('secondchoice_date') }}"
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
                                @endphp
                                <option value="{{ $time }}" {{ old('secondchoice_time') == $time ? 'selected' : '' }}>
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
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('inquirycontent') }}</textarea>
                    @errorText('inquirycontent')
                </div>
                <!-- 流入経路 -->
                <div>
                    <label class="block font-semibold mb-1">流入経路</label>
                    <select name="route" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.routes') as $key => $label)
                            <option value="{{ $key }}" @selected(old('route') == $key)>{{ $label }}</option>
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
                            <option value="{{ $key }}" @selected(old('routedetail') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('routedetail')
                </div>
                <!-- 1週間当たりの平均残業時間 -->
                <div>
                    <label class="block font-semibold mb-1">1週間当たりの平均残業時間</label>
                    <input type="text" name="averageovertimehoursperweek" value="{{ old('averageovertimehoursperweek') }}"
                        placeholder="〇〇時間（例: 10時間）"
                        class="w-full p-2 border rounded bg-white">
                    @errorText('averageovertimehoursperweek')
                </div>
                <!-- 月収 -->
                <div>
                    <label class="block font-semibold mb-1">月収</label>
                    <input type="text" name="monthlyincome" value="{{ old('monthlyincome') }}"
                            placeholder="〇〇万円（例: 30万円）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('monthlyincome')
                </div>
                <!-- 勤続年数 -->
                <div>
                    <label class="block font-semibold mb-1">勤続年数</label>
                    <input type="text" name="lengthofservice" value="{{ old('lengthofservice') }}"
                            placeholder="〇〇年〇〇ヶ月（例: 5年10ヶ月）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('lengthofservice')
                </div>
                <div></div>
                <!-- 相談ID -->
                <div>
                    <label class="block font-semibold mb-1">相談ID</label>
                    <input type="text" name="consultation_id" value="{{ old('consultation_id') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('consultation_id')
                </div>
            </div>

            <!-- ボタン -->
            <div class="flex justify-between items-center mt-6">
                <a href="{{ route('inquiry.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                    一覧に戻る
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    登録する
                </button>
            </div>
        </form>
    </div>
</div>

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