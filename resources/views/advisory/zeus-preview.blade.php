@extends('layouts.app')

@section('content')
<div class="w-full p-6">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">ZEUS継続予約データ 対象一覧</h2>

    <!-- ✅ ダウンロードボタン -->
    <div class="flex justify-end mb-4">
        <a href="{{ route('advisory.zeus.download') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
            データ作成（TXT形式）
        </a>
    </div>

    <!-- 📋 対象データ一覧 -->
    <div class="p-4 border rounded-lg shadow bg-white">
        <div class="overflow-y-auto max-h-80">
            <table class="w-full border-collapse border border-gray-300">
                <thead class="sticky top-0 bg-sky-700 text-white z-10 text-sm shadow-md border-b border-gray-300">
                    <tr>
                        <th class="border p-2">顧問契約：件名</th>
                        <th class="border p-2">クライアント名</th>
                        <th class="border p-2">ステータス</th>
                        <th class="border p-2">契約開始日</th>
                        <th class="border p-2">契約終了日</th>
                        <th class="border p-2">支払方法</th>
                        <th class="border p-2">外部ID</th>
                        <th class="border p-2">引落依頼額</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse ($contracts as $contract)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('advisory.show', $contract->id) }}" class="text-blue-500">
                                    {{ $contract->title }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                <a href="{{ route('client.show', $contract->client_id) }}" class="text-blue-600 hover:underline">
                                    {{ optional($contract->client)->name_kanji }}
                                </a>
                            </td>
                            <td class="border px-2 py-[6px] truncate">
                                {{ config('master.advisory_contracts_statuses')[$contract->status] ?? '未設定' }}
                            </td>
                            <td class="border px-2 py-[6px] truncate">{!! $contract->advisory_start_date ?: '&nbsp;' !!}</td>
                            <td class="border px-2 py-[6px] truncate">{!! $contract->advisory_end_date ?: '&nbsp;' !!}</td>
                            <td class="border px-2 py-[6px] truncate">{{ config('master.payment_methods')[$contract->payment_method] ?? '-' }}</td>
                            <td class="border px-2 py-[6px] truncate">{{ $contract->external_id }}</td>
                            <td class="border px-2 py-[6px] text-right">{{ number_format($contract->withdrawal_request_amount) }}円</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-4">該当する契約はありません。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- ✅ 外枠の外に表示 -->
        <div class="relative mt-6 h-10">
           <!-- 左側：一覧に戻る -->
            <div class="absolute left-0">
                <a href="{{ route('advisory.index') }}" class="text-blue-600 text-sm hover:underline hover:text-blue-800">一覧に戻る</a>
            </div>
        </div>
    </div>
</div>
@endsection