@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

<div class="w-full p-6">
    <h1 class="text-2xl font-bold mb-4">ダッシュボード</h1>


    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">未読タスクコメント</div>
        </div>
        <div class="px-6 py-4 text-sm text-gray-700">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="sticky top-0 bg-blue-50 text-blue-900 z-10 text-sm shadow-md border-b border-gray-500">
                        <tr>
                            <th class="px-2 py-1 border">操作</th>
                            <th class="px-2 py-1 border">タスク名</th>
                            <th class="px-2 py-1 border">コメント作成日</th>
                            <th class="px-2 py-1 border">投稿者</th>
                            <th class="px-2 py-1 border">コメント内容</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($unreadComments as $comment)
                        <tr>
                            <td class="border px-2 py-1">
                                @php
                                    $loginId = auth()->id();
                                    $recipientLabel = $comment->to_id === $loginId ? 'to_id'
                                                    : ($comment->to2_id === $loginId ? 'to2_id'
                                                    : ($comment->to3_id === $loginId ? 'to3_id' : null));
                                @endphp

                                @if ($recipientLabel)
                                    <button onclick="document.getElementById('readModal-{{ $comment->id }}').classList.remove('hidden')"
                                            class="text-blue-600 text-sm hover:underline">👀 既読にする</button>
                                    
                                    {{-- モーダル --}}
                                    <div id="readModal-{{ $comment->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                        <div class="bg-white shadow-lg w-full max-w-md rounded">
                                            <div class="bg-blue-600 text-white px-4 py-2 font-bold border-b">コメントを既読にする</div>
                                            <div class="px-6 py-4 text-sm">
                                                <p class="mb-2">このコメントを既読にしますか？</p>
                                            </div>
                                            <form method="POST" action="{{ route('task.comment.read', ['task' => $comment->task_id, 'comment' => $comment->id]) }}">
                                                @csrf
                                                <input type="hidden" name="redirect_url" value="{{ url()->current() }}">
                                                <input type="hidden" name="recipient" value="{{ $recipientLabel }}">
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
                            </td>
                            <td class="border px-2 py-1">
                                <a href="{{ route('task.show', $comment->task->id) }}" class="text-blue-600 hover:underline">
                                    {{ $comment->task->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-1">
                                {{ $comment->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="border px-2 py-1">
                                {{ optional($comment->from)->name ?: '不明ユーザー' }}
                            </td>
                            <td class="border px-2 py-1 whitespace-normal break-words">
                                {!! nl2br(e(Str::limit($comment->comment, 50, '...'))) !!}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td class="text-center text-gray-500 py-4">該当する未読コメントはありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">📞発着信：未完了タスク</div>
        </div>
        <div class="px-6 py-4 text-sm text-gray-700">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="sticky top-0 bg-blue-50 text-blue-900 z-10 text-sm shadow-md border-b border-gray-500">
                        <tr>
                            <th class="px-2 py-1 border">件名</th>
                            <th class="px-2 py-1 border">作成日</th>
                            <th class="px-2 py-1 border">期限</th>
                            <th class="px-2 py-1 border">ステータス</th>
                            <th class="px-2 py-1 border">orderer</th>
                            <th class="px-2 py-1 border">宛先</th>
                            <th class="px-2 py-1 border">内容</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($phoneTasks as $task)
                    <tr>
                        <td class="border px-2 py-1">
                            <a href="{{ route('task.show', $task->id) }}" class="text-blue-600 hover:underline">
                                {{ $task->title }}
                            </a>
                        </td>
                        <td class="border px-2 py-1">
                            {{ $task->created_at ? $task->created_at->format('Y-m-d H:i') : '-' }}
                        </td>
                        <td class="border px-2 py-1">{{ $task->deadline_date ?? '-' }}</td>
                        <td class="border px-2 py-1">{{ config('master.task_statuses')[$task->status] ?? '-' }}</td>
                        <td class="border px-2 py-1">{{ optional($task->orderer)->name ?? '-' }}</td>
                        <td class="border px-2 py-1">{{ $task->record_to }}</td>
                        <td class="border px-2 py-1 whitespace-normal break-words">
                            {!! nl2br(e(Str::limit($task->content, 50, '...'))) !!}
                        </td>
                    </tr>
                        @empty
                        <tr>
                            <td class="text-center text-gray-500 py-4">該当するタスクはありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">未完了タスク（発着信以外）</div>
        </div>
        <div class="px-6 py-4 text-sm text-gray-700">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300">
                    <thead class="sticky top-0 bg-blue-50 text-blue-900 z-10 text-sm shadow-md border-b border-gray-500">
                        <tr>
                            <th class="px-2 py-1 border">件名</th>
                            <th class="px-2 py-1 border">作成日</th>
                            <th class="px-2 py-1 border">期限</th>
                            <th class="px-2 py-1 border">ステータス</th>
                            <th class="px-2 py-1 border">orderer</th>
                            <th class="px-2 py-1 border">内容</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($todoTasks as $task)
                    <tr>
                        <td class="border px-2 py-1">
                            <a href="{{ route('task.show', $task->id) }}" class="text-blue-600 hover:underline">
                                {{ $task->title }}
                            </a>
                        </td>
                        <td class="border px-2 py-1">
                            {{ $task->created_at ? $task->created_at->format('Y-m-d H:i') : '-' }}
                        </td>
                        <td class="border px-2 py-1">{{ $task->deadline_date ?? '-' }}</td>
                        <td class="border px-2 py-1">{{ config('master.task_statuses')[$task->status] ?? '-' }}</td>
                        <td class="border px-2 py-1">{{ optional($task->orderer)->name ?? '-' }}</td>
                        <td class="border px-2 py-1 whitespace-normal break-words">
                            {!! nl2br(e(Str::limit($task->content, 50, '...'))) !!}
                        </td>
                    </tr>
                        @empty
                        <tr>
                            <td class="text-center text-gray-500 py-4">該当するタスクはありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">問合せ一覧（担当が自分または未設定・未完了）</div>
        </div>
        <div class="px-6 py-4 text-sm text-gray-700">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="sticky top-0 bg-blue-50 text-blue-900 z-10 text-sm shadow-md border-b border-gray-500">
                        <tr>
                            <th class="border p-2 w-3/12">お名前（漢字）</th>
                            <th class="border p-2 w-2/12">担当者</th>
                            <th class="border p-2 w-2/12">問合せ日時</th>
                            <th class="border p-2 w-2/12">流入経路（詳細）</th>
                            <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($inquiries as $inquiry)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('inquiry.show', $inquiry->id) }}" class="text-blue-500">
                                    {{ $inquiry->inquiries_name_kanji }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {!! optional($inquiry->manager)->name ?: '&nbsp;' !!}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ $inquiry->receptiondate ? $inquiry->receptiondate->format('Y-m-d H:i') : '―' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.routedetails')[$inquiry->routedetail] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.inquiry_status')[$inquiry->status] ?? '未設定' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">該当する問合せはありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">相談一覧（担当1～3が自分・未完了）</div>
        </div>
        <div class="px-6 py-4 text-sm text-gray-700">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="sticky top-0 bg-blue-50 text-blue-900 z-10 text-sm shadow-md border-b border-gray-500">
                        <tr>
                            <th class="border p-2 w-4/12">件名</th>
                            <th class="border p-2 w-2/12">区分</th>
                            <th class="border p-2 w-3/12">クライアント名（漢字）</th>
                            <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($consultations as $consultation)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('consultation.show', $consultation->id) }}" class="text-blue-500">
                                    {{ $consultation->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.consultation_parties')[$consultation->consultation_party] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                @if ($consultation->client)
                                    <a href="{{ route('client.show', $consultation->client_id) }}" class="text-blue-600 hover:underline">
                                        {{ optional($consultation->client)->name_kanji }}
                                    </a>
                                @else
                                    （不明）
                                @endif
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.consultation_statuses')[$consultation->status] ?? '未設定' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">該当する相談はありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">受任案件一覧（担当1～3が自分・未完了）</div>
        </div>
        <div class="px-6 py-4 text-sm text-gray-700">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="sticky top-0 bg-blue-50 text-blue-900 z-10 text-sm shadow-md border-b border-gray-500">
                        <tr>
                            <th class="border p-2 w-4/12">件名</th>
                            <th class="border p-2 w-2/12">区分</th>
                            <th class="border p-2 w-3/12">クライアント名（漢字）</th>
                            <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($businesses as $business)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('business.show', $business->id) }}" class="text-blue-500">
                                    {{ $business->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.consultation_parties')[$business->consultation_party] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                @if ($business->client)
                                    <a href="{{ route('client.show', $business->client_id) }}" class="text-blue-600 hover:underline">
                                        {{ optional($business->client)->name_kanji }}
                                    </a>
                                @else
                                    （不明）
                                @endif
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.business_statuses')[$business->status] ?? '未設定' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-4">該当する受任案件はありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">顧問契約（担当1～3が自分・未完了）</div>
        </div>
        <div class="px-6 py-4 text-sm text-gray-700">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="sticky top-0 bg-blue-50 text-blue-900 z-10 text-sm shadow-md border-b border-gray-500">
                        <tr>
                            <th class="border p-2 w-4/12">件名</th>
                            <th class="border p-2 w-2/12">区分</th>
                            <th class="border p-2 w-3/12">クライアント名（漢字）</th>
                            <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($advisoryContracts as $advisory)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('advisory.show', $advisory->id) }}" class="text-blue-500">
                                    {{ $advisory->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.advisory_parties')[$advisory->advisory_party] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                @if ($advisory->client)
                                    <a href="{{ route('client.show', $advisory->client_id) }}" class="text-blue-600 hover:underline">
                                        {{ optional($advisory->client)->name_kanji }}
                                    </a>
                                @else
                                    （不明）
                                @endif
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.advisory_contracts_statuses')[$advisory->status] ?? '未設定' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-4">該当する顧問契約はありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-md font-bold">顧問相談（担当1～3が自分・未完了）</div>
        </div>
        <div class="px-6 py-4 text-sm text-gray-700">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="sticky top-0 bg-blue-50 text-blue-900 z-10 text-sm shadow-md border-b border-gray-500">
                        <tr>
                            <th class="border p-2 w-4/12">件名</th>
                            <th class="border p-2 w-2/12">区分</th>
                            <th class="border p-2 w-3/12">クライアント名（漢字）</th>
                            <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @forelse ($advisoryConsultations as $advisory_consultation)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('advisory_consultation.show', $advisory_consultation->id) }}" class="text-blue-500">
                                    {{ $advisory_consultation->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.advisory_parties')[$advisory_consultation->advisory_party] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                @if ($advisory_consultation->client)
                                    <a href="{{ route('client.show', $advisory_consultation->client_id) }}" class="text-blue-600 hover:underline">
                                        {{ optional($advisory_consultation->client)->name_kanji }}
                                    </a>
                                @else
                                    （不明）
                                @endif
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.advisory_consultations_statuses')[$advisory_consultation->status] ?? '未設定' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-4">該当する顧問相談はありません。</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection