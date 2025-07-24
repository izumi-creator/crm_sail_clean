@extends('layouts.app')

@section('content')
@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

<h2 class="text-2xl font-bold mb-2 text-gray-800">受任案件登録</h2>

<p class="text-sm text-red-600 mb-4">
    ※ トラブル用の画面です。原則、相談詳細画面からステータスを変更して受任案件を登録してください。
</p>

<!-- 外枠カード -->
<div class="p-6 border rounded-lg shadow bg-white">

    <form action="{{ route('business.store') }}" method="POST">
    @csrf

        <!-- ✅見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- ヘッダー -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">受任案件情報</div>

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
                    <select name="client_id"
                            class="select-client w-full"
                            data-old-id="{{ old('client_id') }}"
                            data-old-text="{{ old('client_name_display') }}">
                        <option></option>
                    </select>
                    @errorText('client_id')
                </div>
                <div></div>
                <div class="mt-0 p-4 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800">    
                    新規クライアントの場合は、まず
                    <a href="{{ route('client.create') }}" class="text-blue-600 underline font-semibold">こちらからクライアント登録</a>
                    を行い、その後この画面で再選択してください。
                </div>

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    相談
                </div>
                <!-- 相談 -->
                <div id="existing-consultation-area">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>相談検索</label>
                    <select name="consultation_id"
                            class="select-consultation w-full"
                            data-old-id="{{ old('consultation_id') }}"
                            data-old-text="{{ old('consultation_name_display') }}"> 
                            <option></option>
                    </select>
                    @errorText('consultation_id')
                </div>
                <div></div>
                <div class="mt-0 p-4 bg-yellow-100 border border-yellow-300 rounded text-sm text-yellow-800">    
                    相談未登録の場合は、
                    <a href="{{ route('consultation.create') }}" class="text-blue-600 underline font-semibold">こちらから相談登録</a>
                    を行い、。
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
                        @foreach (config('master.business_statuses') as $key => $label)
                                @if (in_array($key, [1, 2])) {{-- 登録時に選べる値だけ --}}
                                    <option value="{{ $key }}" @selected(old('status') == $key)>{{ $label }}</option>
                                @endif
                        @endforeach
                    </select>
                    @errorText('status')
                </div>
                <!-- 事件概要 -->
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件概要</label>
                    <textarea name="case_summary" rows="4"
                              class="w-full p-2 border rounded bg-white resize-y">{{ old('case_summary') }}</textarea>
                    @errorText('case_summary')
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
                    詳細
                </div>
                <!-- 問い合せ形態 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span>問い合せ形態
                    </label>
                    <select name="inquirytype" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.inquirytypes') as $key => $label)
                            <option value="{{ $key }}" @selected(old('inquirytype') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('inquirytype')
                </div>
                <!-- 相談形態 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        相談形態
                    </label>
                    <select name="consultationtype" class="w-full p-2 border rounded bg-white required">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.consultation_types') as $key => $label)
                            <option value="{{ $key }}" @selected(old('consultationtype') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('consultationtype')
                </div>

                <!-- 事件分野（親） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件分野</label>
                    <select id="case_category" name="case_category" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.case_categories') as $key => $label)
                            <option value="{{ $key }}" @selected(old('case_category') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('case_category')
                </div>
                <!-- 子：事件分野（詳細） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>事件分野（詳細）</label>
                    <select id="case_subcategory" name="case_subcategory" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        {{-- JSで動的に上書き --}}
                    </select>
                    @errorText('case_subcategory')
                </div>

                <!-- 受任日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>受任日</label>
                    <input type="date" name="appointment_date"
                           value="{{ old('appointment_date') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('appointment_date')
                </div>
                <!-- 時効完成日 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">時効完成日</label>
                    <input type="date" name="status_limitday"
                           value="{{ old('status_limitday') }}"
                           class="w-full p-2 border rounded bg-white">
                    @errorText('status_limitday')
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
                        <span class="text-red-500">*</span>弁護士（名前で検索）
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
                        <span class="text-red-500">*</span>パラリーガル（名前で検索）
                    </label>
                    <select name="paralegal_id" class="select-user w-full p-2 border rounded bg-white"
                            data-old-id="{{ old('paralegal_id') }}"
                            data-old-text="{{ old('paralegal_name_display') }}">
                        <option></option>
                    </select>
                    @errorText('paralegal_id')
                </div>

                <!-- 小見出し：見込み -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    見込み
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>見込理由</label>
                    <input type="text" name="feefinish_prospect" value="{{ old('feefinish_prospect') }}" class="w-full p-2 border rounded bg-white">
                    @errorText('feefinish_prospect')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>報酬体系</label>
                    <input type="text" name="feesystem" value="{{ old('feesystem') }}" class="w-full p-2 border rounded bg-white">
                    @errorText('feesystem')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>売上見込</label>
                    <input type="text" name="sales_prospect"
                        value="{{ old('sales_prospect') }}"
                        data-raw="{{ old('sales_prospect') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                    @errorText('sales_prospect')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>売上見込更新日</label>
                    <input type="date" name="sales_reason_updated" value="{{ old('sales_reason_updated') }}" class="mt-1 p-2 border rounded w-full bg-white">
                    @errorText('sales_reason_updated')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>売上見込（初期値）</label>
                    <input type="text" name="feesystem_initialvalue"
                        value="{{ old('feesystem_initialvalue') }}"
                        data-raw="{{ old('feesystem_initialvalue') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                    @errorText('feesystem_initialvalue')
                </div>
                <div></div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>終了時期見込</label>
                    <input type="date" name="enddate_prospect" value="{{ old('enddate_prospect') }}" class="mt-1 p-2 border rounded w-full bg-white">
                    @errorText('enddate_prospect')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>終了時期見込（初期値）</label>
                    <input type="date" name="enddate_prospect_initialvalue" value="{{ old('enddate_prospect_initialvalue') }}" class="mt-1 p-2 border rounded w-full bg-white">
                    @errorText('enddate_prospect_initialvalue')
                </div>                

                <!-- 小見出し：弁護士費用 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    弁護士費用
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>着手金</label>
                    <input type="text" name="deposit"
                        value="{{ old('deposit') }}"
                        data-raw="{{ old('deposit') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                    @errorText('deposit')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>成果報酬</label>
                    <input type="text" name="performance_reward"
                        value="{{ old('performance_reward') }}"
                        data-raw="{{ old('performance_reward') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                    @errorText('performance_reward')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">差額</label>
                    <input type="text" name="difference"
                        placeholder="自動計算：売上見込 - 着手金 - 成果報酬"
                        value="{{ old('difference') }}"
                        data-raw="{{ old('difference') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-gray-100 cursor-not-allowed"
                        readonly>
                    @errorText('difference')
                </div>

                <!-- 小見出し：預り依頼金 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    預り依頼金
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>預り依頼金（予定）</label>
                    <input type="text" name="requestfee_initialvalue"
                        value="{{ old('requestfee_initialvalue') }}"
                        data-raw="{{ old('requestfee_initialvalue') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                    @errorText('requestfee_initialvalue')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>預り依頼金</label>
                    <input type="text" name="requestfee"
                        value="{{ old('requestfee') }}"
                        data-raw="{{ old('requestfee') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-white">
                    @errorText('requestfee')
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">預り金残</label>
                    <input type="text" name="requestfee_balance"
                        placeholder="自動計算：預り依頼金（予定） - 預り依頼金"
                        value="{{ old('requestfee_balance') }}"
                        data-raw="{{ old('requestfee_balance') }}"
                        class="currency-input mt-1 p-2 border rounded w-full bg-gray-100 cursor-not-allowed"
                        readonly>
                    @errorText('requestfee_balance')
                </div>

                <!-- 小見出し：流入経路 -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    流入経路
                </div>
                <!-- 親：流入経路 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>流入経路</label>
                    <select id="route" name="route" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        @foreach (config('master.routes') as $key => $label)
                            <option value="{{ $key }}" @selected(old('route') == $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @errorText('route')
                </div>
                <!-- 子：流入経路（詳細） -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">流入経路（詳細）</label>
                    <select id="routedetail" name="routedetail" class="w-full p-2 border rounded bg-white">
                        <option value="">-- 未選択 --</option>
                        {{-- JSで動的に上書き --}}
                    </select>
                    @errorText('routedetail')
                </div>

                <!-- 紹介者 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        紹介者
                    </label>
                    <input type="text" name="introducer" value="{{ old('introducer') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('introducer')
                </div>
                <!-- 紹介者その他 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        紹介者その他
                    </label>
                    <input type="text" name="introducer_others" value="{{ old('introducer_others') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('introducer_others')
                </div>

                <!-- 小見出し：コメント -->
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    コメント
                </div>
                <!-- コメント -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        コメント
                    </label>
                    <input type="text" name="comment" value="{{ old('comment') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('comment')
                </div>           
                <div>
                <!-- 進捗コメント -->
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        進捗コメント
                    </label>
                    <input type="text" name="progress_comment" value="{{ old('progress_comment') }}"
                           class="w-full p-2 border rounded bg-white required">
                    @errorText('progress_comment')
                </div>
            </div>    
        </div>
        <!-- ボタン -->
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('business.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">
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

    // ▼ 金額フィールドの初期フォーマット（¥ + カンマ区切り）
    document.querySelectorAll('.currency-input').forEach(input => {
        const raw = input.dataset.raw;
        if (raw) {
            input.value = '¥' + Number(raw).toLocaleString();
        }

        // ▼ 入力時にリアルタイム整形（数字以外除去 → カンマ付きに）
        input.addEventListener('input', () => {
            const value = input.value.replace(/[^\d]/g, ''); // 数字以外除去
            input.value = value ? '¥' + Number(value).toLocaleString() : '';

            // 自動計算を実行
            updateCalculatedFields();
        });
    });

    // ▼ 自動計算関数（差額、預り金残）
    function updateCalculatedFields() {
        const parseValue = (selector) => {
            const el = document.querySelector(selector);
            return el ? parseInt((el.value || '').replace(/[^\d]/g, ''), 10) || 0 : 0;
        };

        // ✅ 差額 = 売上見込 - 着手金 - 成果報酬
        const sales = parseValue('input[name="sales_prospect"]');
        const deposit = parseValue('input[name="deposit"]');
        const reward = parseValue('input[name="performance_reward"]');
        const difference = sales - deposit - reward;
        const differenceInput = document.querySelector('input[name="difference"]');
        if (differenceInput) {
            differenceInput.value = '¥' + difference.toLocaleString();
        }

        // 預り金残 = 預り依頼金（予定） - 預り依頼金
        const initial = parseValue('input[name="requestfee_initialvalue"]');
        const current = parseValue('input[name="requestfee"]');
        const balance = initial - current;
        const balanceInput = document.querySelector('input[name="requestfee_balance"]');
        if (balanceInput) {
            balanceInput.value = '¥' + balance.toLocaleString();
        }
    }

    // ▼ フォーム送信時：¥やカンマを除去して送信する
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            form.querySelectorAll('.currency-input').forEach(input => {
                input.value = input.value.replace(/[^\d]/g, '');
            });
        });
    });

    // ▼ 流入経路、事件分野の動的更新
    const dynamicOptions = {
        routedetail: @json($routedetailOptions ?? []),
        casedetail: @json($casedetailOptions ?? []),
        // 他の動的セレクトがあればここに追加
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
        'route', 'routedetail',
        'routedetail',
        "{{ old('routedetail', optional($business ?? null)->routedetail) }}"
    );

    setupDependentSelect(
        'case_category', 'case_subcategory',
        'casedetail',
        "{{ old('case_subcategory', optional($business ?? null)->case_subcategory) }}"
    );
    // 他にも以下のように呼び出し可能にしておけば、JSは再利用できます
    // setupDependentSelect('court', 'court_branch', 'court_branch', old値...);

});
</script>
@endsection