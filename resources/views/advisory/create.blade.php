@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<h2 class="text-2xl font-bold mb-2 text-gray-800">顧問契約登録</h2>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('advisory.store') }}" method="POST">
    @csrf

    <input type="hidden" name="redirect_url" value="{{ request('redirect_url') }}">

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">顧問契約情報</div>

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

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    クライアント
                </div>
                <!-- クライアント -->
                <div id="existing-client-area">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>クライアント検索</label>
                    @if (request('client_id'))
                        <input type="hidden" name="client_id" value="{{ request('client_id') }}">
                        <input type="text"
                               value="{{ \App\Models\Client::find(request('client_id'))?->name_kanji ?? '（不明）' }}"
                               class="w-full p-2 border rounded bg-gray-100 text-gray-500"
                               readonly>
                    @else
                        <select name="client_id"
                                class="select-client w-full"
                                data-old-id="{{ old('client_id') }}"
                                data-old-text="{{ old('client_name_display') }}">
                            <option></option>
                        </select>
                    @endif
                @errorText('client_id')
                </div>
                <div></div>
                <div class="mt-0 p-4 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800">    
                    新規クライアントの場合は、まず
                    <a href="{{ route('client.create') }}" class="text-blue-600 underline font-semibold">こちらからクライアント登録</a>
                    を行い、その後この画面で再選択してください。
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
                        @foreach (config('master.advisory_contracts_statuses') as $key => $label)
                                @if (in_array($key, [1, 2])) {{-- 登録時に選べる値だけ --}}
                                    <option value="{{ $key }}" @selected(old('status') == $key)>{{ $label }}</option>
                                @endif
                        @endforeach
                    </select>
                    @errorText('status')
                </div>
                <!-- 事件概要 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">説明</label>
                    <textarea name="explanation" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('explanation') }}</textarea>
                    @errorText('explanation')
                </div>
                <!-- 特記事項 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                    <textarea name="special_notes" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('special_notes') }}</textarea>
                    @errorText('special_notes')
                </div>                

                <!-- 小見出し：詳細 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    詳細情報
                </div>
                <!-- 契約開始日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">契約開始日</label>
                    <input type="date" name="advisory_start_date"
                           value="{{ old('advisory_start_date') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('advisory_start_date')
                </div>
                <!-- 契約終了日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">契約終了日</label>
                    <input type="date" name="advisory_end_date"
                           value="{{ old('advisory_end_date') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('advisory_end_date')
                </div>
                <!-- 顧問料月額 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">顧問料月額</label>
                    <input type="text" name="amount_monthly"
                        value="{{ old('amount_monthly') }}"
                        data-raw="{{ old('amount_monthly') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                    @errorText('amount_monthly')
                </div>
                <!-- 契約期間（月） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        契約期間（月）
                    </label>
                    <input type="text" name="contract_term_monthly" value="{{ old('contract_term_monthly') }}"
                           class="w-full p-2 border rounded bg-gray-100" readonly>
                    @errorText('contract_term_monthly')
                </div>
                <!-- 初回相談日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">初回相談日</label>
                    <input type="date" name="consultation_firstdate"
                           value="{{ old('consultation_firstdate') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('consultation_firstdate')
                </div>
                <!-- 支払区分 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        支払区分
                    </label>
                    <select name="payment_category" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.payment_categories') as $key => $label)
                            <option value="{{ $key }}" @selected(old('payment_category') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('payment_category')
                </div>
                <!-- 自動引落番号 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        自動引落番号
                    </label>
                    <input type="text" name="adviser_fee_auto" value="{{ old('adviser_fee_auto') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('adviser_fee_auto')
                </div>
                <!-- 支払方法 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        支払方法
                    </label>
                    <select name="payment_method" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.payment_methods') as $key => $label)
                            <option value="{{ $key }}" @selected(old('payment_method') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('payment_method')
                </div>
                <!-- 引落依頼額 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">引落依頼額</label>
                    <input type="text" name="withdrawal_request_amount"
                        value="{{ old('withdrawal_request_amount') }}"
                        data-raw="{{ old('withdrawal_request_amount') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                    @errorText('withdrawal_request_amount')
                </div>
                <!-- 引落内訳 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        引落内訳
                    </label>
                    <input type="text" name="withdrawal_breakdown" value="{{ old('withdrawal_breakdown') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('withdrawal_breakdown')
                </div>
                <!-- 引落更新日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">引落更新日</label>
                    <input type="date" name="withdrawal_update_date"
                           value="{{ old('withdrawal_update_date') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('withdrawal_update_date')
                </div>
                
                <!-- 小見出し：担当 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    担当
                </div>
                <!-- 取扱事務所 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>取扱事務所
                    </label>
                    <select name="office_id" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.offices_id') as $key => $label)
                            <option value="{{ $key }}" @selected(old('office_id') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('office_id')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">GoogleDriveフォルダID</label>
                    <input type="text" name="folder_id" 
                    placeholder="例：1A2B3C4D5E6F7G8H9I0J"
                    value="{{ old('folder_id') }}" class="w-full p-2 border rounded bg-white">
                    @errorText('folder_id')
                </div>

                <!-- 弁護士 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        弁護士（名前で検索）
                    </label>
                    <select name="lawyer_id"
                            class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('lawyer_id') }}"
                            data-old-text="{{ old('lawyer_name_display') }}">
                            <option></option>
                    </select>
                    @errorText('lawyer_id')
                </div>

                <!-- パラリーガル -->
                <div>
                     <label class="block text-sm font-semibold text-gray-700 mb-1">
                        パラリーガル（名前で検索）
                    </label>
                    <select name="paralegal_id" class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('paralegal_id') }}"
                            data-old-text="{{ old('paralegal_name_display') }}">
                        <option></option>
                    </select>
                    @errorText('paralegal_id')
                </div>

                <!-- 小見出し：ソース -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    ソース
                </div>
                <!-- 親：ソース -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        ソース
                    </label>
                    <select  id="source" name="source" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.routes') as $key => $label)
                            <option value="{{ $key }}" @selected(old('source') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('source')
                </div>
                <!-- 子：ソース（詳細） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ソース（詳細）</label>
                    <select id="source_detail" name="source_detail" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        {{-- JSで動的に上書き --}}
                    </select>
                    @errorText('source_detail')
                </div>
                <!-- 紹介者その他 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        紹介者その他
                    </label>
                    <input type="text" name="introducer_others" value="{{ old('introducer_others') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('introducer_others')
                </div>

                <!-- 小見出し：交際情報 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    交際情報
                </div>
                <!-- お中元・お歳暮 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        お中元・お歳暮
                    </label>
                    <select name="gift" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.gifts') as $key => $label)
                            <option value="{{ $key }}" @selected(old('gift') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('gift')
                </div>
                <!-- 年賀状 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        年賀状
                    </label>
                    <select name="newyearscard" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.newyearscards') as $key => $label)
                            <option value="{{ $key }}" @selected(old('newyearscard') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('newyearscard')
                </div>
            </div>    
        </div>
        <!-- ボタン -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('advisory.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
                一覧に戻る
            </a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                登録する
            </button>
        </div>
    </form>
</div>

<script>

document.addEventListener('DOMContentLoaded', function () {

    // ▼ 金額フィールドの初期フォーマット
    document.querySelectorAll('.currency-input').forEach(input => {
        const raw = input.dataset.raw;
        if (raw) {
            input.value = '¥' + Number(raw).toLocaleString();
        }

        input.addEventListener('input', () => {
            const value = input.value.replace(/[^\d]/g, '');
            input.value = value ? '¥' + Number(value).toLocaleString() : '';
        });
    });

    // ▼ 契約期間（月）の自動計算
    function calculateContractTerm() {
        const startInput = document.querySelector('input[name="advisory_start_date"]');
        const endInput = document.querySelector('input[name="advisory_end_date"]');
        const termInput = document.querySelector('input[name="contract_term_monthly"]');

        const start = startInput?.value ? new Date(startInput.value) : null;
        const end = endInput?.value ? new Date(endInput.value) : null;

        if (start && end && end >= start) {
            const yearDiff = end.getFullYear() - start.getFullYear();
            const monthDiff = end.getMonth() - start.getMonth();
            const totalMonths = yearDiff * 12 + monthDiff + 1;
            termInput.value = totalMonths;
        } else {
            termInput.value = '';
        }
    }

    const startInput = document.querySelector('input[name="advisory_start_date"]');
    const endInput = document.querySelector('input[name="advisory_end_date"]');

    if (startInput && endInput) {
        startInput.addEventListener('change', calculateContractTerm);
        endInput.addEventListener('change', calculateContractTerm);
    }

    // ▼ 送信前に契約期間を再計算＋金額整形
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            calculateContractTerm(); // ←ここが追加された部分
            form.querySelectorAll('.currency-input').forEach(input => {
                input.value = input.value.replace(/[^\d]/g, '');
            });
        });
    });

    // ▼ 流入経路（ソース）の動的更新
    const dynamicOptions = {
        routedetail: @json($routedetailOptions ?? []),
        // ここに court_branch など他の構成も後で追加できる
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

    // ▼ 呼び出し例（初期値も渡せる）
    setupDependentSelect(
        'source', 'source_detail',
        'routedetail',
        "{{ old('source_detail', optional($advisory ?? null)->source_detail) }}"
    );

    // 他にも以下のように呼び出し可能にしておけば、JSは再利用できます
    // setupDependentSelect('court', 'court_branch', 'court_branch', old値...);

});

</script>
@endsection