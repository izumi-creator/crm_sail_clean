@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">裁判所対応詳細</h2>

    <!-- 裁判所対応詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            @if (auth()->user()->role_type == 1)
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
            @endif
        </div>

        <!-- ✅ 裁判所対応情報の見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">

            <!-- 見出し -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border">裁判所対応情報</div>

            <!-- 内容 -->
            <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    基本情報
                </div>
                <!-- ステータス -->
               <div>
                   <label class="block text-sm font-semibold text-gray-700 mb-1">
                       <span class="text-red-500">*</span> ステータス
                  </label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! config('master.court_tasks_statuses')[$courtTask->status] ?? '&nbsp;' !!}
                     </div>
                </div>
                <!-- ステータス詳細 -->
                <div>
                    <label class="font-bold">ステータス詳細</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $courtTask->status_detail ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- タスク名 -->
                <div>
                   <label class="block text-sm font-semibold text-gray-700 mb-1">
                       <span class="text-red-500">*</span> タスク名
                    </label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $courtTask->task_title ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- タスク分類 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        <span class="text-red-500">*</span> タスク分類
                    </label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! config('master.court_task_categories')[$courtTask->task_category] ?? '&nbsp;' !!}
                        </div>
                </div>
                <!-- タスク内容 -->
                <div class="col-span-2">
                   <label class="block text-sm font-semibold text-gray-700 mb-1">
                       <span class="text-red-500">*</span> タスク内容
                    </label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        <pre class="mt-1 p-2 min-h-[100px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $courtTask->task_content }}</pre>
                    </div>
                </div>
                <!-- 担当弁護士 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当弁護士</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($courtTask->lawyer)->name ?: '&nbsp;' !!}</div>
                </div>
                <!-- 担当パラリーガル -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">担当パラリーガル</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($courtTask->paralegal)->name ?: '&nbsp;' !!}</div>
                </div>
                <!-- 期限 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"> 期限</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {{ $courtTask->deadline ? $courtTask->deadline->format('Y-m-d H:i') : '―' }}
                    </div>
                </div>
                <!-- 受任案件：件名 -->
                <div>
                    <label class="font-bold">受任案件：件名</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        @if ($courtTask->business)
                            <a href="{{ route('business.show', $courtTask->business->id) }}"
                               class="text-blue-600 underline hover:text-blue-800">
                                {{ $courtTask->business->title }}
                            </a>
                        @elseif ($courtTask->business_id)
                            <span class="text-gray-400">（削除された受任案件）</span>
                        @else
                            {{-- 空白（何も表示しない） --}}
                            &nbsp;
                        @endif
                    </div>
                </div>
                <!-- 移動時間 -->         
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1"> 移動時間</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {{ $courtTask->move_time ? $courtTask->move_time->format('H:i') : '―' }}
                    </div>
                </div>
                <!-- メモ欄 -->
                <div class="col-span-2">
                   <label class="block text-sm font-semibold text-gray-700 mb-1">
                       <span class="text-red-500">*</span> メモ欄
                    </label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        <pre class="mt-1 p-2 min-h-[100px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $courtTask->memo }}</pre>
                    </div>
                </div>

                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    裁判所情報
                </div>

                <!-- 裁判所名 -->
                <div>
                    <label class="font-bold">裁判所名</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        @if ($courtTask->court)
                            <a href="{{ route('court.show', $courtTask->court->id) }}"
                               class="text-blue-600 underline hover:text-blue-800">
                                {{ $courtTask->court->court_name }}
                            </a>
                        @elseif ($courtTask->court_id)
                            <span class="text-gray-400">（削除された裁判所）</span>
                        @else
                            {{-- 空白（何も表示しない） --}}
                            &nbsp;
                        @endif
                    </div>
                </div>
                <!-- 担当係 -->
                <div>
                    <label class="font-bold">担当係</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $courtTask->department ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- 担当裁判官 -->
                <div>
                    <label class="font-bold">担当裁判官</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $courtTask->judge ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- 担当書記官 -->
                <div>
                    <label class="font-bold">担当書記官</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $courtTask->clerk ?: '&nbsp;' !!}
                     </div>
                </div>
                <!-- 電話（直通） -->
                <div>
                    <label class="font-bold">電話（直通）</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $courtTask->tel_direct ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- FAX（直通） -->
                <div>
                    <label class="font-bold">FAX（直通）</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $courtTask->fax_direct ?: '&nbsp;' !!}
                    </div>
                </div>
                <!-- メール（直通） -->
                <div>
                    <label class="font-bold">メール（直通）</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $courtTask->email_direct ?: '&nbsp;' !!}
                    </div>
                </div>
            </div>
        </div>
        <!-- ✅ 外枠の外に表示 -->
        <div class="relative mt-6 h-10">
           <!-- 左側：一覧に戻る -->
            <div class="absolute left-0">
                <a href="{{ route('business.show', ['business' => $business->id]) }}#tab-courtTask"
                   class="text-blue-600 hover:underline hover:text-blue-800">
                    一覧に戻る
                </a>
            </div>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('court_task.update', $courtTask->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">裁判所対応編集</div>

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
                <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                    <div class="col-span-2 bg-orange-300 py-2 px-6 -mx-6">
                        区分・分類
                    </div>
                    <!-- 区分 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 区分
                        </label>
                        <select name="relatedparties_party" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_parties') as $key => $label)
                                <option value="{{ $key }}" @selected($relatedparty->relatedparties_party == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('relatedparties_party')
                    </div>
                    <!-- 分類 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 分類
                        </label>
                        <select name="relatedparties_class" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_classes') as $key => $label)
                                <option value="{{ $key }}" @selected($relatedparty->relatedparties_class == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('relatedparties_class')
                    </div>
                    <!-- 種別 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 種別
                        </label>
                        <select name="relatedparties_type" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_types') as $key => $label)
                                <option value="{{ $key }}" @selected($relatedparty->relatedparties_type == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('relatedparties_type')
                    </div>
                    <!-- 立場 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">
                            <span class="text-red-500">*</span> 立場
                        </label>
                        <select name="relatedparties_position" class="w-full p-2 border rounded bg-white">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.relatedparties_positions') as $key => $label)
                                <option value="{{ $key }}" @selected($relatedparty->relatedparties_position == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('relatedparties_position')
                    </div>

                    <div class="col-span-2 bg-orange-300 py-2 px-6 -mx-6">
                        説明
                    </div>

                    <!-- 立場詳細 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">立場詳細</label>
                        <input type="text" name="relatedparties_position_details" value="{{ $relatedparty->relatedparties_position_details }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('relatedparties_position_details')
                    </div>
                    <!-- 説明 -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">説明</label>
                        <input type="text" name="relatedparties_explanation" value="{{ $relatedparty->relatedparties_explanation }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('relatedparties_explanation')
                    </div>

                    <div class="col-span-2 bg-orange-300 py-2 px-6 -mx-6">
                        詳細情報
                    </div>

                    <!-- 関係者名（漢字） -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">関係者名（漢字）</label>
                        <input type="text" name="relatedparties_name_kanji" value="{{ $relatedparty->relatedparties_name_kanji }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('relatedparties_name_kanji')
                    </div>
                    <!-- 関係者名（ふりがな） -->
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">関係者名（ふりがな）</label>
                        <input type="text" name="relatedparties_name_kana" value="{{ $relatedparty->relatedparties_name_kana }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('relatedparties_name_kana')
                    </div>
                    <!-- 携帯 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">携帯</label>
                        <input type="text" name="mobile_number" value="{{ $relatedparty->mobile_number }}"
                                placeholder="ハイフンなしで入力（例: 09012345678）"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('mobile_number')
                    </div>
                    <!-- 電話 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話</label>
                        <input type="text" name="phone_number" value="{{ $relatedparty->phone_number }}"
                               placeholder="ハイフンなしで入力（例: 0312345678）"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('phone_number')
                    </div>
                    <!-- 電話2 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">電話2</label>
                        <input type="text" name="phone_number2" value="{{ $relatedparty->phone_number2 }}"
                               placeholder="ハイフンなしで入力（例: 0312345678）"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('phone_number2')
                    </div>
                    <!-- FAX -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">FAX</label>
                        <input type="text" name="fax" value="{{ $relatedparty->fax }}"
                               placeholder="ハイフンなしで入力（例: 0312345678）"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('fax')
                    </div>
                    <!-- メール -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">メール</label>
                        <input type="email" name="email" value="{{ $relatedparty->email }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('email')
                    </div>
                    <!-- メール2 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">メール2</label>
                        <input type="email" name="email2" value="{{ $relatedparty->email2 }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('email2')
                    </div>
                    <!-- 郵便番号 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">郵便番号</label>
                        <input type="text" name="relatedparties_postcode" value="{{ $relatedparty->relatedparties_postcode }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('relatedparties_postcode')
                    </div>
                    <!-- 住所 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">住所</label>
                        <input type="text" name="relatedparties_address" value="{{ $relatedparty->relatedparties_address }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('relatedparties_address')
                    </div>
                    <!-- 住所2 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">住所2</label>
                        <input type="text" name="relatedparties_address2" value="{{ $relatedparty->relatedparties_address2 }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('relatedparties_address2')
                    </div>
                    <!-- 勤務先 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">勤務先</label>
                        <input type="text" name="placeofwork" value="{{ $relatedparty->placeofwork }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('placeofwork')
                    </div>
                    <!-- 担当者名（漢字） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当者名（漢字）</label>
                        <input type="text" name="manager_name_kanji" value="{{ $relatedparty->manager_name_kanji }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('manager_name_kanji')
                    </div>
                    <!-- 担当者名（ふりがな） -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">担当者名（ふりがな）</label>
                        <input type="text" name="manager_name_kana" value="{{ $relatedparty->manager_name_kana }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('manager_name_kana')
                    </div>
                    <!-- 役職 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">役職</label>
                        <input type="text" name="manager_post" value="{{ $relatedparty->manager_post }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('manager_post')
                    </div>
                    <!-- 部署 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">部署</label>
                        <input type="text" name="manager_department" value="{{ $relatedparty->manager_department }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('manager_department')
                    </div>

                    <div class="col-span-2 bg-orange-300 py-2 px-6 -mx-6">
                        関連先
                    </div>
                    
                    <!-- クライアントID -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">クライアント</label>
                        <select name="client_id"
                                class="select-client-edit w-full"
                                data-initial-id="{{ $relatedparty->client?->id }}"
                                data-initial-text="{{ $relatedparty->client?->name_kanji ?? '' }}">
                            <option></option>
                        </select>
                        <option></option>
                        @errorText('client_id')
                    </div>
                    <!-- 相談: 件名 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">相談: 件名</label>
                        <select name="consultation_id"
                                class="select-consultation-edit w-full"
                                data-initial-id="{{ $relatedparty->consultation_id ?? '' }}"
                                data-initial-text="{{ optional($relatedparty->consultation)->title ?? '' }}">
                            <option></option>
                        </select>
                        @errorText('consultation_id')
                    </div>
                    <!-- 受任案件: 件名 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">受任案件: 件名</label>
                        <select name="business_id"
                                class="select-business-edit w-full"
                                data-initial-id="{{ $relatedparty->business_id ?? '' }}"
                                data-initial-text="{{ optional($relatedparty->business)->title ?? '' }}">
                            <option></option>
                        </select>
                        @errorText('business_id')
                    </div>
                    <!-- 顧問相談ID -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">顧問相談ID</label>
                        <input type="text" name="advisory_id" value="{{ $relatedparty->advisory_id }}"
                               class="w-full p-2 border rounded bg-white">
                        @errorText('advisory_id')
                    </div>
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('relatedparty.show', $relatedparty->id) }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">関係者削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの関係者を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('relatedparty.destroy', $relatedparty->id) }}">
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