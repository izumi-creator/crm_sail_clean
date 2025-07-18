@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif
@if (session('debug_participants'))
    <div class="bg-gray-100 p-2 mb-2 rounded text-sm">
        <strong>participants:</strong>
        <pre>{{ print_r(session('debug_participants'), true) }}</pre>
    </div>
@endif

<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">折衝履歴登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('negotiation.store') }}" method="POST">
    @csrf

    <input type="hidden" name="redirect_url" value="{{ request('redirect_url') }}">

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">折衝履歴情報</div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                        <ul class="list-disc pl-6">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <!-- 入力フィールド -->
            <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                <!-- 小見出し：クライアント -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    関係先
                </div>

                <!-- 関係先選択 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>関係先
                    </label>
                    @if(request('related_party'))
                        <!-- 関係先が決まっている場合：hidden＋readonly表示 -->
                        <input type="hidden" name="related_party" value="{{ request('related_party') }}">
                        <input type="text"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               value="{{ config('master.related_parties')[request('related_party')] ?? '不明' }}"
                               readonly>
                    @else
                        <select name="related_party" id="related_party" class="w-full p-2 border rounded bg-white">
                            <option value="">選択してください</option>
                            @foreach(config('master.related_parties') as $key => $label)
                                <option value="{{ $key }}" {{ old('related_party') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('related_party')
                    @endif
                </div>

                <div></div>

                <!-- 各関係先に応じたselect2（初期は非表示） -->
                <div id="consultation_select_area" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>相談の件名</label>
                    @if(request('related_party') == '1' && request('consultation_id'))
                        <input type="hidden" name="consultation_id" value="{{ request('consultation_id') }}">
                        <input type="text"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               value="{{ \App\Models\Consultation::find(request('consultation_id'))?->title ?? '（不明）' }}"
                               readonly>
                    @else
                        <select name="consultation_id"
                                class="select-consultation w-full"
                                data-old-id="{{ old('consultation_id') }}"
                                data-old-text="{{ old('consultation_name_display') }}">
                            <option></option>
                        </select>
                        @errorText('consultation_id')
                    @endif
                </div>            
            
                <div id="business_select_area" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>受任案件の件名</label>
                    @if(request('related_party') == '2' && request('business_id'))
                        <input type="hidden" name="business_id" value="{{ request('business_id') }}">
                        <input type="text"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               value="{{ \App\Models\Business::find(request('business_id'))?->title ?? '（不明）' }}"
                               readonly>
                    @else                                      
                    <select name="business_id"
                            class="select-business w-full"
                            data-old-id="{{ old('business_id') }}"
                            data-old-text="{{ old('business_name_display') }}">
                        <option></option>
                    </select>
                    @errorText('business_id')
                    @endif
                </div>
            
            
                <div id="advisory_contract_select_area" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>顧問契約の件名</label>
                    @if(request('related_party') == '3' && request('advisory_contract_id'))
                        <input type="hidden" name="advisory_contract_id" value="{{ request('advisory_contract_id') }}">
                        <input type="text"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               value="{{ \App\Models\AdvisoryContract::find(request('advisory_contract_id'))?->title ?? '（不明）' }}"
                               readonly>
                    @else
                    <select name="advisory_contract_id"
                            class="select-advisory w-full"
                            data-old-id="{{ old('advisory_contract_id') }}"
                            data-old-text="{{ old('advisory_contract_name_display') }}">
                        <option></option>
                    </select>
                    @errorText('advisory_contract_id')
                    @endif
                </div>
            
            
                <div id="advisory_consultation_select_area" style="display: none;">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>顧問相談の件名</label>
                    @if(request('related_party') == '4' && request('advisory_consultation_id'))
                        <input type="hidden" name="advisory_consultation_id" value="{{ request('advisory_consultation_id') }}">
                        <input type="text"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               value="{{ \App\Models\AdvisoryConsultation::find(request('advisory_consultation_id'))?->title ?? '（不明）' }}"
                               readonly>
                    @else
                    <select name="advisory_consultation_id"
                            class="select-advisory-consultation w-full"
                            data-old-id="{{ old('advisory_consultation_id') }}"
                            data-old-text="{{ old('advisory_consultation_name_display') }}">
                        <option></option>
                    </select>
                    @errorText('advisory_consultation_id')
                    @endif
                </div>
                
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  基本情報
                </div>
                <!-- 件名 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>件名
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('title')
                </div>
                <!-- ステータス -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>ステータス
                    </label>
                    <select name="status" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.task_statuses') as $key => $label)
                            <option value="{{ $key }}" @selected(old('status') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('status')
                </div>
                <!-- 登録日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">登録日</label>
                    <input type="date" name="record_date"
                           value="{{ old('record_date') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('record_date')
                </div>

                <!-- 大区分（親） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>大区分</label>
                    <select id="record1" name="record1" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.records_1') as $key => $label)
                            <option value="{{ $key }}" @selected(old('record1') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('record1')
                </div>
                <!-- 子：小区分 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>小区分</label>
                    <select id="record2" name="record2" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        {{-- JSで動的に上書き --}}
                    </select>
                    @errorText('record2')
                </div>

                <!-- 既読チェック（チェックボックス） -->
                <div>
                    <label class="inline-flex items-center">
                        <input type="hidden" name="already_read" value="0">
                        <input type="checkbox" name="already_read" value="1"
                               @checked(old('already_read') == 1)>
                        <span class="ml-2">既読チェック</span>
                    </label>
                    @errorText('already_read')
                </div>

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  当事者
                </div>
                <!-- orderer -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        orderer（名前で検索）
                    </label>
                    <select name="orderer_id"
                            class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('orderer_id') }}"
                            data-old-text="{{ old('orderer_name_display') }}">
                            <option></option>
                    </select>
                    @errorText('orderer_id')
                </div>
                <!-- worker -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>worker（名前で検索）
                    </label>
                    <select name="worker_id"
                            class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('worker_id') }}"
                            data-old-text="{{ old('worker_name_display') }}">
                            <option></option>
                    </select>
                    @errorText('worker_id')
                </div>

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  内容
                </div>
                <!-- 内容 -->
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">内容</label>
                    <textarea name="content" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('content') }}</textarea>
                    @errorText('content')
                </div>
                <!-- 添付名1 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        添付名1
                    </label>
                    <input type="text" name="attachment1_title" value="{{ old('attachment1_title') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('attachment1_title')
                </div>
                <!-- 添付リンク1 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        添付リンク1
                    </label>
                    <input type="text" name="link1" value="{{ old('link1') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('link1')
                </div>
                <!-- 添付名2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        添付名2
                    </label>
                    <input type="text" name="attachment2_title" value="{{ old('attachment2_title') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('attachment2_title')
                </div>
                <!-- 添付リンク2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        添付リンク2
                    </label>
                    <input type="text" name="link2" value="{{ old('link2') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('link2')
                </div>
                <!-- 添付名3 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        添付名3
                    </label>
                    <input type="text" name="attachment3_title" value="{{ old('attachment3_title') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('attachment3_title')
                </div>
                <!-- 添付リンク3 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        添付リンク3
                    </label>
                    <input type="text" name="link3" value="{{ old('link3') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('link3')
                </div>

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  電話・履歴通知
                </div>
                 <!-- 電話通知（チェックボックス） -->
                <div>
                    <label class="inline-flex items-center">
                        <input type="hidden" name="phone_request" value="0">
                        <input type="checkbox" name="phone_request" value="1"
                               @checked(old('phone_request') == 1)>
                        <span class="ml-2">電話通知</span>
                    </label>
                    @errorText('phone_request')
                </div>
                <!-- 宛先 -->
                <div>
                    <label class="block font-semibold mb-1">
                        宛先
                    </label>
                    <input type="text" name="record_to" value="{{ old('record_to') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('record_to')
                </div>
                <!-- 通知タイプ -->
                <div>
                    <label class="block font-semibold mb-1">
                        通知タイプ
                    </label>
                    <select name="notify_type" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.notify_types') as $key => $label)
                            <option value="{{ $key }}" @selected(old('notify_type') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('notify_type')
                </div>
                <!-- 電話番号 -->
                <div>
                    <label class="block font-semibold mb-1">電話番号</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number')
                </div>
                 <!-- 担当に通知 -->
                <div>
                    <label class="inline-flex items-center">
                        <input type="hidden" name="notify_person_in" value="0">
                        <input type="checkbox" name="notify_person_in" value="1"
                               @checked(old('notify_person_in') == 1)>
                        <span class="ml-2">担当に通知</span>
                    </label>
                    @errorText('notify_person_in')
                </div>
                <div></div>
                <!-- 着信電話番号 -->
                <div>
                    <label class="block font-semibold mb-1">着信電話番号</label>
                    <input type="text" name="phone_to" value="{{ old('phone_to') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('phone_to')
                </div>
                <!-- 発信電話番号 -->
                <div>
                    <label class="block font-semibold mb-1">発信電話番号</label>
                    <input type="text" name="phone_from" value="{{ old('phone_from') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('phone_from')
                </div>
                <!-- 着信内線番号 -->
                <div>
                    <label class="block font-semibold mb-1">着信内線番号</label>
                    <input type="text" name="naisen_to" value="{{ old('naisen_to') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('naisen_to')
                </div>
                <!-- 発信内線番号 -->
                <div>
                    <label class="block font-semibold mb-1">発信内線番号</label>
                    <input type="text" name="naisen_from" value="{{ old('naisen_from') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('naisen_from')
                </div>
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                  メモ
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">メモ欄</label>
                    <textarea name="memo" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('memo') }}</textarea>
                    @errorText('memo')
                </div>
            </div>        
        </div>
        <!-- ボタン -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('negotiation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                一覧に戻る
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                登録する
            </button>
        </div>
    </form>
</div>

<script>
$(function () {
    function toggleSelectAreas() {
        const selected = $('#related_party').val() || $('input[name="related_party"]').val(); // hiddenも対応

        // 全部非表示
        $('[id$="_select_area"]').hide();

        // 該当select表示
        if (selected === '1') $('#consultation_select_area').show();
        if (selected === '2') $('#business_select_area').show();
        if (selected === '3') $('#advisory_contract_select_area').show();
        if (selected === '4') $('#advisory_consultation_select_area').show();
    }

    // セレクトボックス変更時にも対応
    $('#related_party').on('change', toggleSelectAreas);

    // 初期表示にも対応（hiddenフィールド含む）
    toggleSelectAreas();

    // ▼ record1 → record2 の動的連動処理（Vanilla JS）

    const dynamicOptions = {
        record2: @json($record2Options ?? []),
    };

    function setupDependentSelect(parentId, childId, optionKey, selectedValue = null) {
        const parent = document.getElementById(parentId);
        const child = document.getElementById(childId);
        if (!parent || !child || !dynamicOptions[optionKey]) return;

        function update() {
            const selected = parent.value;
            const options = dynamicOptions[optionKey][selected] || [];
            child.innerHTML = '<option value="">-- 未選択 --</option>';
            options.forEach(opt => {
                const el = document.createElement('option');
                el.value = opt.id;
                el.textContent = opt.label;
                child.appendChild(el);
            });
            if (selectedValue) {
                child.value = selectedValue;
            }
        }

        parent.addEventListener('change', update);
        update(); // 初期化
    }

    setupDependentSelect(
        'record1',
        'record2',
        'record2',
        "{{ old('record2', optional($negotiation ?? null)->record2) }}"
    );

});
</script>

@endsection