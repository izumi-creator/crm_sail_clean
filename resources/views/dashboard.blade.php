@extends('layouts.app')

@section('content')
<div class="w-full p-6">
    <h1 class="text-2xl font-bold mb-4">ダッシュボード</h1>

<div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
    <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
        <div class="text-md font-bold">タスク・折衝履歴（自分が担当で未完了・取り下げ以外）</div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 px-6 py-4 text-sm text-gray-700">
        {{-- 📋 タスク一覧 --}}
        <div>
            <div class="bg-blue-50 text-blue-900 px-4 py-2 font-bold border flex items-center justify-between">
                <div>📋 タスク一覧（{{ $tasks->count() }}件）</div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-h-64 overflow-y-auto pr-2">
                @foreach ($tasks as $task)
                    <div class="border rounded shadow-sm p-3 bg-white text-sm leading-tight">
                        <div class="font-bold text-sky-700 mb-1">{{ $task->title }}</div>
                        <div><span class="font-semibold">大区分:</span> {{ config('master.records_1')[$task->record1] ?? '―' }}</div>
                        <div><span class="font-semibold">関連先区分（登録時）:</span> {{ config('master.related_parties')[$task->related_party] ?? '―' }}</div>
                        <div><span class="font-semibold">期限:</span> {{ $task->deadline_date }}</div>
                        <div><span class="font-semibold">ステータス:</span> {{ config('master.task_statuses')[$task->status] ?? '―' }}</div>
                        <div class="mt-2">
                            <a href="{{ route('task.show', $task->id) }}" class="text-blue-600 hover:underline text-sm">詳細を見る</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 📝 折衝履歴 --}}
        <div>
            <div class="bg-blue-50 text-blue-900 px-4 py-2 font-bold border flex items-center justify-between">
                <div>📋 折衝履歴（{{ $negotiations->count() }}件）</div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 max-h-64 overflow-y-auto pr-2">
                @foreach ($negotiations as $negotiation)
                    <div class="border rounded shadow-sm p-3 bg-white text-sm leading-tight">
                        <div class="font-bold text-sky-700 mb-1">{{ $negotiation->title }}</div>
                        <div><span class="font-semibold">大区分:</span> {{ config('master.records_1')[$negotiation->record1] ?? '―' }}</div>
                        <div><span class="font-semibold">関連先区分（登録時）:</span> {{ config('master.related_parties')[$negotiation->related_party] ?? '―' }}</div>
                        <div><span class="font-semibold">登録日:</span> {{ $negotiation->record_date }}</div>
                        <div><span class="font-semibold">ステータス:</span> {{ config('master.task_statuses')[$negotiation->status] ?? '―' }}</div>
                        <div class="mt-2">
                            <a href="{{ route('negotiation.show', $negotiation->id) }}" class="text-blue-600 hover:underline text-sm">詳細を見る</a>
                        </div>
                    </div>
                @endforeach
            </div>
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
                        <th class="border p-2 w-1/12">ID</th>
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
                        <td class="border px-2 py-[6px] truncate">{{ $inquiry->id }}</td>
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
                        <td colspan="6" class="text-center text-gray-500 py-4">該当する問合せはありません。</td>
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
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-4/12">件名</th>
                        <th class="border p-2 w-2/12">区分</th>
                        <th class="border p-2 w-3/12">クライアント名（漢字）</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse ($consultations as $consultation)
                    <tr>
                        <td class="border px-2 py-[6px] truncate">{{ $consultation->id }}</td>
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
                        <td colspan="6" class="text-center text-gray-500 py-4">該当する相談はありません。</td>
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
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-4/12">件名</th>
                        <th class="border p-2 w-2/12">区分</th>
                        <th class="border p-2 w-3/12">クライアント名（漢字）</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse ($businesses as $business)
                    <tr>
                        <td class="border px-2 py-[6px] truncate">{{ $business->id }}</td>
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
                        <td colspan="6" class="text-center text-gray-500 py-4">該当する受任案件はありません。</td>
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
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-4/12">件名</th>
                        <th class="border p-2 w-2/12">区分</th>
                        <th class="border p-2 w-3/12">クライアント名（漢字）</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse ($advisoryContracts as $advisory)
                    <tr>
                        <td class="border px-2 py-[6px] truncate">{{ $advisory->id }}</td>
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
                        <td colspan="6" class="text-center text-gray-500 py-4">該当する顧問契約はありません。</td>
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
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-4/12">件名</th>
                        <th class="border p-2 w-2/12">区分</th>
                        <th class="border p-2 w-3/12">クライアント名（漢字）</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse ($advisoryConsultations as $advisory_consultation)
                    <tr>
                        <td class="border px-2 py-[6px] truncate">{{ $advisory_consultation->id }}</td>
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
                        <td colspan="6" class="text-center text-gray-500 py-4">該当する顧問相談はありません。</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection