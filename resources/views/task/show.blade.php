@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">タスク詳細</h2>

    <!-- タスク詳細カード -->
    <div class="p-6 border rounded-lg shadow bg-white">
        <!-- 上部ボタン -->
        <div class="flex justify-end space-x-2 mb-4">
            <!-- W⇔O 切替ボタン -->
            <button onclick="document.getElementById('swapModal').classList.remove('hidden')"
                class="bg-blue-300 hover:bg-blue-500 text-black px-4 py-2 rounded min-w-[100px]">
                🔁 W⇔O 入替
            </button>
            <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
            @if (auth()->user()->role_type == 1)
            <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
            @endif
        </div>
        <!-- ✅ タスク情報の見出し＋内容を枠で囲む -->
        <div class="border border-gray-300 overflow-hidden">
            <!-- 見出し -->
            <div class="bg-sky-700 text-white px-4 py-2 font-bold border">タスク情報</div>
            <!-- 内容 -->
            <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                    基本情報
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">件名</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $task->title ?: '&nbsp;' !!}</div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ステータス</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $task->status ? config('master.task_statuses')[$task->status] : '&nbsp;' !!}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">登録日</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $task->record_date ?: '&nbsp;' !!}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">大区分</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $task->record1 ? config('master.records_1')[$task->record1] : '&nbsp;' !!}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">小区分</label>
                    <div class="mt-1 p-2 border rounded bg-gray-50">
                        {!! $task->record2 ? config('master.records_2')[$task->record2] : '&nbsp;' !!}
                    </div>
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
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($task->orderer)->name ?: '&nbsp;' !!}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">worker</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">{!! optional($task->worker)->name ?: '&nbsp;' !!}</div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">期限日</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->deadline_date ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">期限時間</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {{ $task->deadline_time
                                        ? \Carbon\Carbon::createFromFormat('H:i:s', $task->deadline_time)->format('H:i')
                                        : '―' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- アコーディオンヘッダー -->
                <div class="col-span-2 mt-2 -mx-6">
                    <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                        <span>コメント履歴：{{ $task->comments->count() }}件（クリックで開閉）</span>
                        <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                    </div>
                
                    <!-- アコーディオン中身 -->
                    <div class="accordion-content hidden pt-4 px-6 text-sm leading-relaxed font-sans text-black">
                        <!-- ボタン -->
                        <div class="text-right mb-4">
                            <button onclick="document.getElementById('commentModal').classList.remove('hidden')"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">
                                💬 新規コメント
                            </button>
                        </div>
                    
                        <!-- コメントカード -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse($task->comments as $comment)
                                <div class="border border-gray-300 p-4 rounded bg-gray-50 shadow-sm relative">
                                    <!-- 上部操作ボタン -->
                                    <div class="absolute top-2 right-2 flex space-x-2">
                                        @if ($comment->from_id === auth()->id())
                                            <button onclick="document.getElementById('deleteCommentModal-{{ $comment->id }}').classList.remove('hidden')"
                                                    class="text-red-500 text-sm hover:underline">🗑 削除</button>
                                        @endif
                                            
                                        @if ($comment->recipient_field)
                                            @if ($comment->already_read_status)
                                                <span class="text-green-600 text-sm">✅ 既読済み</span>
                                            @else
                                                <button onclick="document.getElementById('readModal-{{ $comment->id }}').classList.remove('hidden')"
                                                        class="text-blue-600 text-sm hover:underline">👀 既読にする</button>
                                            @endif
                                        @endif
                                    </div>
                                
                                    <!-- 表示内容 -->
                                    <div class="text-sm">
                                        🗨 {{ optional($comment->from)->name ?? '不明ユーザー' }} さん（{{ $comment->created_at->format('Y/m/d H:i') }}）
                                    </div>
                                    <div class="text-sm mt-1">
                                        <span class="font-semibold text-black">宛先：</span>
                                        {{ optional($comment->to)->name ?? '-' }}
                                        @if ($comment->to2) / {{ optional($comment->to2)->name }} @endif
                                        @if ($comment->to3) / {{ optional($comment->to3)->name }} @endif
                                    </div>
                                    <div class="mt-1 flex text-sm text-gray-700">
                                        <span class="text-black w-12 shrink-0">内容：</span>
                                        <pre class="whitespace-pre-wrap break-words font-sans text-black">{{ $comment->comment }}</pre>
                                    </div>
                                
                                    <!-- 未読者表示 -->
                                    <div class="mt-1 text-sm text-black">
                                        未読者：{{ $comment->unread_names->isNotEmpty() ? $comment->unread_names->implode('、') : 'なし' }}
                                    </div>
                                
                                    <!-- コメント削除モーダル -->
                                    @if ($comment->from_id === auth()->id())
                                        <div id="deleteCommentModal-{{ $comment->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                            <div class="bg-white shadow-lg w-full max-w-md">
                                                <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">コメント削除</div>
                                                <div class="px-6 py-4 text-sm">
                                                    <p class="mb-2">このコメントを削除しますか？</p>
                                                    <p class="mb-2">この操作は取り消せません。</p>
                                                </div>
                                                <form method="POST" action="{{ route('task.comment.destroy', ['task' => $task->id, 'comment' => $comment->id]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="flex justify-end space-x-2 px-6 pb-6">
                                                        <button type="button"
                                                                onclick="document.getElementById('deleteCommentModal-{{ $comment->id }}').classList.add('hidden')"
                                                                class="px-4 py-2 bg-gray-300 text-black rounded">キャンセル</button>
                                                        <button type="submit"
                                                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 min-w-[100px]">削除</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- 既読確認モーダル -->
                                    @if ($comment->recipient_field && ! $comment->already_read_status)
                                        <div id="readModal-{{ $comment->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                            <div class="bg-white shadow-lg w-full max-w-md rounded">
                                                <div class="bg-blue-600 text-white px-4 py-2 font-bold border-b">コメントを既読にする</div>
                                                <div class="px-6 py-4 text-sm">
                                                    <p class="mb-2">このコメントを既読にしますか？</p>
                                                </div>
                                                <form method="POST" action="{{ route('task.comment.read', ['task' => $task->id, 'comment' => $comment->id]) }}">
                                                    @csrf
                                                    <input type="hidden" name="recipient" value="{{ $comment->recipient_field }}">
                                                    <div class="flex justify-end space-x-2 px-6 pb-6">
                                                        <button type="button"
                                                                onclick="document.getElementById('readModal-{{ $comment->id }}').classList.add('hidden')"
                                                                class="px-4 py-2 bg-gray-300 text-black rounded">キャンセル</button>
                                                        <button type="submit"
                                                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 min-w-[100px]">既読にする</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-gray-500">コメントはまだありません。</p>
                            @endforelse
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
                                <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $task->content }}</pre>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付名1</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->attachment1_title ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク1</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    @if (!empty($task->link1))
                                        <a href="{{ $task->link1 }}" class="text-blue-600 underline break-all" target="_blank" rel="noopener">
                                            {{ $task->link1 }}
                                        </a>
                                    @else
                                        &nbsp;
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付名2</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->attachment2_title ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク2</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    @if (!empty($task->link2))
                                        <a href="{{ $task->link2 }}" class="text-blue-600 underline break-all" target="_blank" rel="noopener">
                                            {{ $task->link2 }}
                                        </a>
                                    @else
                                        &nbsp;
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付名3</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->attachment3_title ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク3</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    @if (!empty($task->link3))
                                        <a href="{{ $task->link3 }}" class="text-blue-600 underline break-all" target="_blank" rel="noopener">
                                            {{ $task->link3 }}
                                        </a>
                                    @else
                                        &nbsp;
                                    @endif
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">運送業者</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->carrier ? config('master.carriers')[$task->carrier] : '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">追跡番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->tracking_number ?: '&nbsp;' !!}
                                </div>
                            </div>                            

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">追跡ページ</label>
                                @php
                                    $trackingNumber = preg_replace('/[^0-9]/', '', $task->tracking_number);
                                @endphp
                                @if ($task->carrier == 1)
                                    <a href="https://trackings.post.japanpost.jp/services/srv/search/?requestNo1={{ $trackingNumber }}"
                                       target="_blank" class="text-blue-600 underline">▶ 日本郵便追跡ページ</a>

                                @elseif ($task->carrier == 2)
                                    <form method="POST" action="https://toi.kuronekoyamato.co.jp/cgi-bin/tneko" target="_blank" class="inline">
                                        @csrf
                                        <input type="hidden" name="number01" value="{{ $trackingNumber }}">
                                        <button type="submit" class="text-blue-600 underline">▶ ヤマト追跡ページ</button>
                                    </form>                               
                                @elseif ($task->carrier == 3)
                                    <a href="https://k2k.sagawa-exp.co.jp/p/sagawa/web/okurijoinput.jsp?okurijoNo={{ $trackingNumber }}"
                                       target="_blank" class="text-blue-600 underline">▶ 佐川急便追跡ページ</a>
                                @else
                                    <span class="text-gray-500">（運送業者未登録）</span>
                                @endif
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
                                    <input type="checkbox" disabled class="form-checkbox text-blue-600" {{ $task->phone_request ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700">電話通知</span>
                                </label>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">宛先</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->record_to ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">通知タイプ</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->notify_type ? config('master.notify_types')[$task->notify_type] : '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->phone_number ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">着信電話番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->phone_to ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">発信電話番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->phone_from ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">着信内線番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->naisen_to ?: '&nbsp;' !!}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">発信内線番号</label>
                                <div class="mt-1 p-2 border rounded bg-gray-50">
                                    {!! $task->naisen_from ?: '&nbsp;' !!}
                                </div>
                            </div>
                        </div>
                    </div>                   
                </div>

                <div class="col-span-2 mt-2 -mx-6">
                    <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                        <span>メモ（クリックで開閉）</span>
                        <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                    </div>
                    <div class="accordion-content hidden pt-4 px-6">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">メモ欄</label>
                                <pre class="mt-1 p-2 min-h-[75px] border rounded bg-gray-50 whitespace-pre-wrap text-sm font-sans leading-relaxed">{{ $task->memo }}</pre>
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
                                     {!! $task->related_party ? config('master.related_parties')[$task->related_party] : '&nbsp;' !!}
                                 </div>
                             </div>
                             <div></div>
                             <!-- 相談：件名-->
                             <div>
                                 <label class="font-bold">相談：件名</label>
                                 <div class="mt-1 p-2 border rounded bg-gray-50">
                                     @if ($task->consultation)
                                         <a href="{{ route('consultation.show', $task->consultation->id) }}"
                                            class="text-blue-600 underline hover:text-blue-800">
                                             {{ $task->consultation->title }}
                                         </a>
                                     @elseif ($task->consultation_id)
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
                                     @if ($task->business)
                                         <a href="{{ route('business.show', $task->business->id) }}"
                                            class="text-blue-600 underline hover:text-blue-800">
                                             {{ $task->business->title }}
                                         </a>
                                     @elseif ($task->business_id)
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
                                     @if ($task->advisoryContract)
                                         <a href="{{ route('advisory.show', $task->advisoryContract->id) }}"
                                            class="text-blue-600 underline hover:text-blue-800">
                                             {{ $task->advisoryContract->title }}
                                         </a>
                                     @elseif ($task->advisory_contract_id)
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
                                     @if ($task->advisoryConsultation)
                                         <a href="{{ route('advisory_consultation.show', $task->advisoryConsultation->id) }}"
                                            class="text-blue-600 underline hover:text-blue-800">
                                             {{ $task->advisoryConsultation->title }}
                                         </a>
                                     @elseif ($task->advisory_consultation_id)
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
                <a href="{{ route('task.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
            </div>
        </div>
    </div>

    <!-- 編集モーダル -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-3xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('task.update', $task->id) }}">
                @csrf
                @method('PUT')

                <input type="hidden" name="_modal" value="edit">
            
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
                        <input type="text" name="title" value="{{ $task->title }}" class="w-full p-2 border rounded bg-white" required>
                        @errorText('title')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>ステータス</label>
                        <select name="status" class="mt-1 p-2 border rounded w-full bg-white" required>
                            @foreach (config('master.task_statuses') as $key => $value)
                                <option value="{{ $key }}" {{ $task->status == $key ? 'selected' : '' }}>{{ $value }}</option>
                            @endforeach
                        </select>
                        @errorText('status')
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">登録日</label>
                        <input type="date" name="record_date" value="{{ $task->record_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                        @errorText('record_date')
                    </div>

                    <!-- 親：大区分 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>大区分</label>
                        <select id="record1" name="record1" class="w-full p-2 border rounded bg-white required">
                            <option value="">-- 未選択 --</option>
                            @foreach (config('master.records_1') as $key => $label)
                                <option value="{{ $key }}" @selected($task->record1 == $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @errorText('record1')
                    </div>               
                    <!-- 子：小区分 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>小区分</label>
                        <select id="record2" name="record2" class="w-full p-2 border rounded bg-white required">
                            {{-- JSで大区分に応じた小区分を動的に生成 --}}
                            <option value="">-- 未選択 --</option>
                            {{-- JSで上書き --}}
                        </select>
                        @errorText('record2')
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
                                            data-initial-id="{{ $task->orderer_id }}"
                                            data-initial-text="{{ optional($task->orderer)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('orderer_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1"><span class="text-red-500">*</span>worker</label>
                                    <select name="worker_id"
                                            class="select-user-edit w-full required"
                                            data-initial-id="{{ $task->worker_id }}"
                                            data-initial-text="{{ optional($task->worker)->name }}">
                                        <option></option>
                                    </select>
                                    @errorText('worker_id')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">期限日</label>
                                    <input type="date" name="deadline_date" value="{{ $task->deadline_date }}" class="mt-1 p-2 border rounded w-full bg-white">
                                    @errorText('deadline_date')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">期限時間</label>
                                    <select name="deadline_time" class="w-full p-2 border rounded bg-white">
                                        <option value="">-- 時間を選択 --</option>
                                        @for ($h = 9; $h <= 20; $h++)
                                            @foreach (['00', '15', '30', '45'] as $m)
                                                @php
                                                    $time = sprintf('%02d:%s', $h, $m);
                                                    $selected = false;
                                                    if ($task->deadline_time) {
                                                        $deadline_time = \Carbon\Carbon::createFromFormat('H:i:s', $task->deadline_time)->format('H:i');
                                                        $selected = $deadline_time === $time;
                                                    }
                                                @endphp
                                                <option value="{{ $time }}" {{ $selected ? 'selected' : '' }}>
                                                    {{ $time }}
                                                </option>
                                            @endforeach
                                        @endfor
                                    </select>
                                    @errorText('deadline_time')
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
                                    <textarea name="content" rows="4" class="w-full p-2 border rounded bg-white">{{ $task->content }}</textarea>
                                    @errorText('content')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付名1</label>
                                    <input type="text" name="attachment1_title" value="{{ $task->attachment1_title }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('attachment1_title')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク1</label>
                                    <input type="text" name="link1" value="{{ $task->link1 }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('link1')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付名2</label>
                                    <input type="text" name="attachment2_title" value="{{ $task->attachment2_title }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('attachment2_title')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク2</label>
                                    <input type="text" name="link2" value="{{ $task->link2 }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('link2')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付名3</label>
                                    <input type="text" name="attachment3_title" value="{{ $task->attachment3_title }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('attachment3_title')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">添付リンク3</label>
                                    <input type="text" name="link3" value="{{ $task->link3 }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('link3')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">運送業者</label>
                                    <select name="carrier" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">選択してください</option>
                                        @foreach (config('master.carriers') as $key => $value)
                                            <option value="{{ $key }}" {{ $task->carrier == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('carrier')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">追跡番号</label>
                                    <input type="text" name="tracking_number" value="{{ $task->tracking_number }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('tracking_number')
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
                                            {{ $task->phone_request == 1 ? 'checked' : '' }}
                                            class="form-checkbox text-blue-600">
                                        <span class="ml-2 text-sm text-gray-700">電話通知チェック</span>
                                    </label>
                                    @errorText('phone_request')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">宛先</label>
                                    <input type="text" name="record_to" value="{{ $task->record_to }}" class="w-full p-2 border rounded bg-white">
                                    @errorText('record_to')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">通知タイプ</label>
                                    <select name="notify_type" class="mt-1 p-2 border rounded w-full bg-white">
                                        <option value="">選択してください</option>
                                        @foreach (config('master.notify_types') as $key => $value)
                                            <option value="{{ $key }}" {{ $task->notify_type == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @errorText('notify_type')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">電話番号</label>
                                    <input type="text" name="phone_number" value="{{ $task->phone_number }}" 
                                        placeholder="ハイフンなしで入力（例: 0312345678）"
                                        class="w-full p-2 border rounded bg-white">
                                    @errorText('phone_number')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">着信電話番号</label>
                                    <input type="text" name="phone_to" value="{{ $task->phone_to }}" 
                                    placeholder="ハイフンなしで入力（例: 0312345678）"
                                    class="w-full p-2 border rounded bg-white">
                                    @errorText('phone_to')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">発信電話番号</label>
                                    <input type="text" name="phone_from" value="{{ $task->phone_from }}" 
                                    placeholder="ハイフンなしで入力（例: 0312345678）"
                                    class="w-full p-2 border rounded bg-white">
                                    @errorText('phone_from')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">着信内線番号</label>
                                    <input type="text" name="naisen_to" value="{{ $task->naisen_to }}" 
                                    placeholder="ハイフンなしで入力（例: 0312345678）"
                                    class="w-full p-2 border rounded bg-white">
                                    @errorText('naisen_to')
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">発信内線番号</label>
                                    <input type="text" name="naisen_from" value="{{ $task->naisen_from }}" 
                                    placeholder="ハイフンなしで入力（例: 0312345678）"
                                    class="w-full p-2 border rounded bg-white">
                                    @errorText('naisen_from')
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between col-span-2 bg-orange-300 py-2 px-6 cursor-pointer accordion-toggle">
                            <span>メモ（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">メモ欄</label>
                                    <textarea name="memo" rows="3" class="mt-1 p-2 border rounded w-full bg-white">{{ $task->memo }}</textarea>
                                    @errorText('memo')
                                </div>
                            </div>
                        </div>
                    </div>                    
                </div>
                <!-- ボタン -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('task.show', $task->id) }}"
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
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">タスク削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこのタスクを削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('task.destroy', $task->id) }}">
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

    <!-- コメント投稿モーダル -->
    <div id="commentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-2xl rounded max-h-[90vh] overflow-y-auto">
            <form method="POST" action="{{ route('task.comment.store', ['task' => $task->id]) }}">
                @csrf

                <input type="hidden" name="_modal" value="comment">

                <!-- ヘッダー -->
                <div class="bg-blue-600 text-white px-4 py-2 font-bold border-b">新規コメント</div>

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

                <!-- フォーム内容 -->
                <div class="p-6 text-sm space-y-4">

                    <!-- コメント内容 -->
                    <div>
                        <label class="block font-semibold mb-1">コメント内容</label>
                        <textarea name="comment" rows="2" class="w-full p-2 border rounded bg-white" required>{{ old('comment') }}</textarea>
                        @errorText('comment')
                    </div>

                    <!-- 宛先1〜3（Select2） -->
                    @for ($i = 1; $i <= 3; $i++)
                        @php
                            $field = 'to' . ($i === 1 ? '' : $i) . '_id';
                            $display = $field . '_name_display';
                        @endphp
                        <div>
                            <label class="block font-semibold mb-1">宛先{{ $i }}</label>
                            <select name="{{ $field }}"
                                    class="select-user w-full"
                                    data-old-id="{{ old($field) }}"
                                    data-old-text="{{ old($display) }}">
                                <option></option>
                            </select>
                            @errorText($field)
                        </div>
                    @endfor

                </div>

                <!-- フッター -->
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <a href="{{ route('task.show', $task->id) }}"
                       class="px-4 py-2 bg-gray-300 text-black rounded">
                       キャンセル
                    </a>
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        投稿する
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- WorkerとOrdererの入れ替えモーダル -->
    <div id="swapModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-md">
            <div class="bg-blue-600 text-white px-4 py-2 font-bold border-b">担当者・依頼者の入替</div>
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">Worker と Orderer を入れ替えますか？</p>
                <p class="mb-2">この操作は即時反映されます。</p>
            </div>

            <form method="POST" action="{{ route('task.swap_worker_orderer', $task->id) }}">
                @csrf
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <button type="button" onclick="document.getElementById('swapModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 text-black rounded min-w-[100px] text-center">
                        キャンセル
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 min-w-[100px]">
                        入れ替える
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
        const modal = '{{ old('_modal') }}';

        if (modal === 'edit') {
            document.getElementById('editModal')?.classList.remove('hidden');
        }
        if (modal === 'comment') {
            document.getElementById('commentModal')?.classList.remove('hidden');
        }

        document.querySelectorAll('.accordion-content').forEach(content => {
            content.classList.remove('hidden');
            const icon = content.previousElementSibling?.querySelector('.accordion-icon');
            icon?.classList.add('rotate-180');
        });
    });
</script>
@endif

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