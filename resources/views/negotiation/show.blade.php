@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">折衝履歴詳細</h2>

    <!-- 折衝履歴詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            @if (auth()->user()->role_type == 1)
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
            @endif
        </div>
        <!-- ✅ 折衝履歴情報の見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">
            <!-- 見出し -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border">折衝履歴情報</div>
            <!-- 内容 -->
            <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    基本情報
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">件名</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $negotiation->title ?: '&nbsp;' !!}</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $negotiation->status ? config('master.task_statuses')[$negotiation->status] : '&nbsp;' !!}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">登録日</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $negotiation->record_date ?: '&nbsp;' !!}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">大区分</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $negotiation->record1 ? config('master.records_1')[$negotiation->record1] : '&nbsp;' !!}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">小区分</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $negotiation->record2 ? config('master.records_2')[$negotiation->record2] : '&nbsp;' !!}
                    </div>
                </div>
                <div>
                    <label class="inline-flex items-center">
                        <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $negotiation->already_read ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">既読チェック</span>
                    </label>
                </div>
                <div class="col-span-2 mt-2 -mx-6">
                    <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                        <span>当事者（クリックで開閉）</span>
                        <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                    </div>
                    <div class="accordion-content hidden pt-4 px-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">orderer</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($negotiation->orderer)->name ?: '&nbsp;' !!}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">worker</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($negotiation->worker)->name ?: '&nbsp;' !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-2 mt-2 -mx-6">
                    <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                        <span>内容（クリックで開閉）</span>
                        <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                    </div>
                    <div class="accordion-content hidden pt-4 px-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">タスク内容</label>
                                <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $negotiation->content }}</pre>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付名1</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->attachment1_title ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク1</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    @if (!empty($negotiation->link1))
                                        <a href="{{ $negotiation->link1 }}" class="text-blue-600 underline break-all" target="_blank" rel="noopener">
                                            {{ $negotiation->link1 }}
                                        </a>
                                    @else
                                        &nbsp;
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付名2</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->attachment2_title ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク2</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    @if (!empty($negotiation->link2))
                                        <a href="{{ $negotiation->link2 }}" class="text-blue-600 underline break-all" target="_blank" rel="noopener">
                                            {{ $negotiation->link2 }}
                                        </a>
                                    @else
                                        &nbsp;
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付名3</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->attachment3_title ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク3</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    @if (!empty($negotiation->link3))
                                        <a href="{{ $negotiation->link3 }}" class="text-blue-600 underline break-all" target="_blank" rel="noopener">
                                            {{ $negotiation->link3 }}
                                        </a>
                                    @else
                                        &nbsp;
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-span-2 mt-2 -mx-6">
                    <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                        <span>電話・履歴通知（クリックで開閉）</span>
                        <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                    </div>
                    <div class="accordion-content hidden pt-4 px-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $negotiation->phone_request ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">電話通知</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">宛先</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->record_to ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">通知タイプ</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->notify_type ? config('master.notify_types')[$negotiation->notify_type] : '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->phone_number ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $negotiation->notify_person_in ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">担当者に通知</span>
                                </label>
                            </div>
                            <div></div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">着信電話番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->phone_to ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">発信電話番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->phone_from ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">着信内線番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->naisen_to ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">発信内線番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $negotiation->naisen_from ?: '&nbsp;' !!}
                                </div>
                            </div>
                        </div>
                    </div>                   
                </div>

                <div class="col-span-2 mt-2 -mx-6">
                    <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                        <span>関連先情報（クリックで開閉）</span>
                        <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                    </div>
                    <div class="accordion-content hidden pt-4 px-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                 <label class="block text-sm font-semibold text-gray-700 mb-1">関連先（登録時）</label>
                                 <div class="mt-1 p-2 border rounded bg-gray-50">
                                     {!! $negotiation->related_party ? config('master.related_parties')[$negotiation->related_party] : '&nbsp;' !!}
                                 </div>
                             </div>
                             <div></div>
                             <!-- 相談：件名-->
                             <div>
                                 <label class="font-bold">相談：件名</label>
                                 <div class="mt-1 p-2 border rounded bg-gray-50">
                                     @if ($negotiation->consultation)
                                         <a href="{{ route('consultation.show', $negotiation->consultation->id) }}"
                                            class="text-blue-600 underline hover:text-blue-800">
                                             {{ $negotiation->consultation->title }}
                                         </a>
                                     @elseif ($negotiation->consultation_id)
                                         <span class="text-gray-400">（削除された相談）</span>
                                     @else
                                         <span class="text-gray-400">（紐づけなし）</span>
                                     @endif
                                 </div>
                             </div>
                             <!-- 受任案件：件名 -->
                             <div>
                                 <label class="font-bold">受任案件：件名</label>
                                 <div class="mt-1 p-2 border rounded bg-gray-50">
                                     @if ($negotiation->business)
                                         <a href="{{ route('business.show', $negotiation->business->id) }}"
                                            class="text-blue-600 underline hover:text-blue-800">
                                             {{ $negotiation->business->title }}
                                         </a>
                                     @elseif ($negotiation->business_id)
                                         <span class="text-gray-400">（削除された受任案件）</span>
                                     @else
                                         <span class="text-gray-400">（紐づけなし）</span>
                                     @endif
                                 </div>
                             </div>
                             <!-- 顧問契約ID -->
                             <div>
                                 <label class="font-bold">顧問契約：件名</label>
                                 <div class="mt-1 p-2 border rounded bg-gray-50">
                                     @if ($negotiation->advisoryContract)
                                         <a href="{{ route('advisory.show', $negotiation->advisoryContract->id) }}"
                                            class="text-blue-600 underline hover:text-blue-800">
                                             {{ $negotiation->advisoryContract->title }}
                                         </a>
                                     @elseif ($negotiation->advisory_contract_id)
                                         <span class="text-gray-400">（削除された顧問契約）</span>
                                     @else
                                         <span class="text-gray-400">（紐づけなし）</span>
                                     @endif
                                 </div>
                             </div>
                             <!-- 顧問相談ID -->
                             <div>
                                 <label class="font-bold">顧問相談：件名</label>
                                 <div class="mt-1 p-2 border rounded bg-gray-50">
                                     @if ($negotiation->advisoryConsultation)
                                         <a href="{{ route('advisory_consultation.show', $negotiation->advisoryConsultation->id) }}"
                                            class="text-blue-600 underline hover:text-blue-800">
                                             {{ $negotiation->advisoryConsultation->title }}
                                         </a>
                                     @elseif ($negotiation->advisory_consultation_id)
                                         <span class="text-gray-400">（削除された顧問相談）</span>
                                     @else
                                         <span class="text-gray-400">（紐づけなし）</span>
                                     @endif
                                 </div>
                             </div>
                       </div>
                    </div>                   
                </div>

            </div>
        </div>
        <!-- ✅ 外枠の外に表示 -->
        <div class="relative mt-6 h-10">
           <!-- 左側：一覧に戻る -->
            <div class="absolute left-0">
                <a href="{{ route('negotiation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
            </div>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('negotiation.update', $negotiation->id) }}">
                @csrf
                @method('PUT')
            
                <!-- モーダル見出し -->
                <div class="bg-amber-600 text-white px-4 py-2 font-bold border-b">タスク編集</div>

                <!-- ✅ エラーボックスをgrid外に出す -->
                @if ($errors->any())
                <div class="p-6 pt-4 -mb-4 text-sm">
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
                         基本情報
                     </div>
                     <div class="col-span-2 bg-blue-50 border border-blue-300 text-blue-800 text-sm rounded px-4 py-3 mb-2">
                        <p class="mt-1">
                            データのリレーション関係があるため、関連先情報は変更できません<br>
                            クローズ、または削除の上、新規登録をお願いします<br>
                        </p>
                    </div>                     
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>件名</label>
                        <input type="text" name="title" value="{{ $negotiation->title }}" class="w-full p-2 border rounded bg-white" required>
                        @errorText('title')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <select name="status" class="mt-1 p-2 border rounded w-full bg-white" required>
                            @foreach (config('master.task_statuses') as $key => $value)
                                <option value="{{ $key }}" {{ $negotiation->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('status')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">登録日</label>
                        <input type="date" name="record_date" value="{{ $negotiation->record_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('record_date')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>大区分</label>
                        <select name="record1" class="mt-1 p-2 border rounded w-full bg-white" required>
                            @foreach (config('master.records_1') as $key => $value)
                                <option value="{{ $key }}" {{ $negotiation->record1 == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('record1')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>小区分</label>
                        <select name="record2" class="mt-1 p-2 border rounded w-full bg-white" required>
                            @foreach (config('master.records_2') as $key => $value)
                                <option value="{{ $key }}" {{ $negotiation->record2 == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('record2')
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="hidden" name="already_read" value="0">
                            <input type="checkbox" name="already_read" value="1"
                                {{ $negotiation->already_read == 1 ? 'checked' : '' }}
                                class="form-checkbox text-blue-600">
                            <span class="ml-2 text-sm text-gray-700">既読チェック</span>
                        </label>
                        @errorText('already_read')
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>当事者（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">orderer</label>
                                    <select name="orderer_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $negotiation->orderer_id }}"
                                            data-initial-text="{{ optional($negotiation->orderer)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('orderer_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>worker</label>
                                    <select name="worker_id"
                                            class="select-user-edit w-full"
                                            data-initial-id="{{ $negotiation->worker_id }}"
                                            data-initial-text="{{ optional($negotiation->worker)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('worker_id')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>内容（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">内容</label>
                                    <textarea name="content" rows="4" class="w-full p-2 border rounded bg-white">{{ $negotiation->content }}</textarea>
                                    @errorText('content')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付名1</label>
                                    <input type="text" name="attachment1_title" value="{{ $negotiation->attachment1_title }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('attachment1_title')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク1</label>
                                    <input type="text" name="link1" value="{{ $negotiation->link1 }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('link1')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付名2</label>
                                    <input type="text" name="attachment2_title" value="{{ $negotiation->attachment2_title }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('attachment2_title')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク2</label>
                                    <input type="text" name="link2" value="{{ $negotiation->link2 }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('link2')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付名3</label>
                                    <input type="text" name="attachment3_title" value="{{ $negotiation->attachment3_title }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('attachment3_title')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク3</label>
                                    <input type="text" name="link3" value="{{ $negotiation->link3 }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('link3')
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>電話・履歴通知（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="phone_request" value="0">
                                        <input type="checkbox" name="phone_request" value="1"
                                            {{ $negotiation->phone_request == 1 ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">電話通知チェック</span>
                                    </label>
                                    @errorText('phone_request')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">宛先</label>
                                    <input type="text" name="record_to" value="{{ $negotiation->record_to }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('record_to')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">通知タイプ</label>
                                    <select name="notify_type" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">選択してください</option>
                                        @foreach (config('master.notify_types') as $key => $value)
                                            <option value="{{ $key }}" {{ $negotiation->notify_type == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('notify_type')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号</label>
                                    <input type="text" name="phone_number" value="{{ $negotiation->phone_number }}" 
                                        placeholder="ハイフンなしで入力（例: 0312345678）"
                                        class="w-full p-2 border rounded bg-white">
                                    @errorText('phone_number')
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="notify_person_in" value="0">
                                        <input type="checkbox" name="notify_person_in" value="1"
                                            {{ $negotiation->notify_person_in == 1 ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">担当者に通知</span>
                                    </label>
                                    @errorText('notify_person_in')
                                </div>
                                <div></div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">着信電話番号</label>
                                    <input type="text" name="phone_to" value="{{ $negotiation->phone_to }}" 
                                    placeholder="ハイフンなしで入力（例: 0312345678）"
                                    class="w-full p-2 border rounded bg-white">
                                    @errorText('phone_to')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">発信電話番号</label>
                                    <input type="text" name="phone_from" value="{{ $negotiation->phone_from }}" 
                                    placeholder="ハイフンなしで入力（例: 0312345678）"
                                    class="w-full p-2 border rounded bg-white">
                                    @errorText('phone_from')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">着信内線番号</label>
                                    <input type="text" name="naisen_to" value="{{ $negotiation->naisen_to }}" 
                                    placeholder="ハイフンなしで入力（例: 0312345678）"
                                    class="w-full p-2 border rounded bg-white">
                                    @errorText('naisen_to')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">発信内線番号</label>
                                    <input type="text" name="naisen_from" value="{{ $negotiation->naisen_from }}" 
                                    placeholder="ハイフンなしで入力（例: 0312345678）"
                                    class="w-full p-2 border rounded bg-white">
                                    @errorText('naisen_from')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('negotiation.show', $negotiation->id) }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">折衝履歴削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの折衝履歴を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('negotiation.destroy', $negotiation->id) }}">
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
        document.querySelectorAll('.accordion-content').forEach(content => {
            content.classList.remove('hidden');
            const icon = content.previousElementSibling?.querySelector('.accordion-icon');
            icon?.classList.add('rotate-180');
        });
    });
</script>
@endif

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ▽ 1. アコーディオン制御  
    const toggles = document.querySelectorAll('.accordion-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.accordion-icon');
            if (content && content.classList.contains('accordion-content')) {
                content.classList.toggle('hidden');
                icon?.classList.toggle('rotate-180');
            }
        });
    });
});
</script>
@endsection