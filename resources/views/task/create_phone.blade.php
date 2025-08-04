@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif


<!-- タイトル -->
<h2 class="text-2xl font-bold mb-4 text-gray-800">電話発着信タスク登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('task.store.phone') }}" method="POST">
    @csrf

    <input type="hidden" name="redirect_url" value="{{ request('redirect_url') }}">
    <input type="hidden" name="record1" value="1">
    <input type="hidden" name="phone_request" value="1">

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">タスク情報</div>

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
                <!-- 小見出し -->
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
        
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>大区分
                    </label>
                    <select class="w-full p-2 border rounded bg-gray-100 text-gray-600" disabled>
                        <option value="1">☎ 電話</option>
                    </select>
                </div>
            
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>小区分
                    </label>
                    <select name="record2" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        <option value="1" {{ old('record2') == '1' ? 'selected' : '' }}>↗ 発信</option>
                        <option value="2" {{ old('record2') == '2' ? 'selected' : '' }}>↙ 受信</option>
                    </select>
                    @errorText('record2')
                </div>

                <!-- 登録日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">登録日</label>
                    <input type="date" name="record_date"
                           value="{{ old('record_date', \Carbon\Carbon::today()->format('Y-m-d')) }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('record_date')
                </div>
            
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
            
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">orderer（名前で検索）</label>
                    <select name="orderer_id" class="select-user w-full"
                            data-old-id="{{ old('orderer_id', auth()->id()) }}"
                            data-old-text="{{ old('orderer_name_display', auth()->user()->name) }}">
                        <option></option>
                    </select>
                    @errorText('orderer_id')
                </div>
            
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>worker（名前で検索）
                    </label>
                    <select name="worker_id"
                            class="select-user w-full p-2 border rounded bg-white required"
                            data-old-id="{{ old('worker_id') }}"
                            data-old-text="{{ old('worker_name_display') }}">
                            <option></option>
                    </select>
                    @errorText('worker_id')
                </div>
            
                <!-- 宛先 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        宛先
                    </label>
                    <input type="text" name="record_to" value="{{ old('record_to') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('record_to')
                </div>

                <!-- 電話番号 -->
                <div>
                    <label class="block font-semibold mb-1">電話番号</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}"
                            placeholder="ハイフンなしで入力（例: 0312345678）"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('phone_number')
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        通知タイプ
                    </label>
                    <select name="notify_type" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.notify_types') as $key => $label)
                            <option value="{{ $key }}" @selected(old('notify_type') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('notify_type')
                </div>
            
                <!-- 内容 -->
                <div class="col-span-2">
                    <label class="block font-semibold mb-1">内容</label>
                    <textarea name="content" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('content') }}</textarea>
                    @errorText('content')
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('task.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
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
        "{{ old('record2', optional($task ?? null)->record2) }}"
    );

});
</script>

@endsection