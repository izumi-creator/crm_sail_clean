@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full p-6"> <!-- ✅ ダッシュボードと統一 -->
        <h2 class="text-2xl font-bold mb-4 text-gray-800">問合せ一覧</h2>

        <!-- 🔍 検索フォーム -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <form method="GET" action="{{ route('inquiry.index') }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold">お名前（漢字）</label>
                        <input type="text" name="inquiries_name_kanji" value="{{ request('inquiries_name_kanji') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">お名前（ふりがな）</label>
                        <input type="text" name="inquiries_name_kana" value="{{ request('inquiries_name_kana') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">問合せ日</label>
                        <input type="date" name="receptiondate" value="{{ request('receptiondate') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">ステータス</label>
                        <select name="status" class="w-full border px-3 py-2 rounded">
                            <option value="">選択してください</option>
                            @foreach (config('master.inquiry_status') as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == (string)$key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <!-- ボタンエリア -->
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded min-w-[100px]">検索</button>
                    <a href="{{ route('inquiry.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">検索条件クリア</a>
                    <a href="{{ route('inquiry.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
                </div>
            </form>
        </div>

<!-- 📋 検索結果（ダッシュボードと同じ設定 + ページネーション内包） -->
<div class="mb-10">
    <h2 class="text-xl font-semibold mb-4">検索結果（{{ $inquiries->total() }}件）</h2>
    <div class="p-4 border rounded-lg shadow bg-white">
        <div class="overflow-y-auto max-h-48"> <!-- ✅ 高さをダッシュボードと統一 -->
            <table class="w-full border-collapse border border-gray-300 table-fixed">
                <thead class="sticky top-0 bg-sky-700 text-white z-10 text-sm shadow-md border-b border-gray-300">
                    <tr>
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-4/12">お名前（漢字）</th>
                        <th class="border p-2 w-3/12">問合せ日時</th>
                        <th class="border p-2 w-2/12">流入経路（詳細）</th>
                        <th class="border p-2 w-2/12">ステータス</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($inquiries as $inquiry)
                    <tr>
                        <td class="border px-2 py-[6px] truncate">{{ $inquiry->id }}</td>
                        <td class="border px-2 py-[6px] truncate">
                            <a href="{{ route('inquiry.show', $inquiry->id) }}" class="text-blue-500">
                                {{ $inquiry->inquiries_name_kanji }}
                            </a>
                        </td>
                        <td class="border px-2 py-[6px] truncate">{{ $inquiry->receptiondate ? $inquiry->receptiondate->format('Y-m-d H:i') : '―' }}</td>
                        <td class="border px-2 py-[6px] truncate">
                            {{ config('master.routedetails')[$inquiry->routedetail] ?? '未設定' }}
                        </td>
                        <td class="border px-2 py-[6px] truncate">
                            {{ config('master.inquiry_status')[$inquiry->status] ?? '未設定' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ✅ ページネーションを検索結果カード内に配置 -->
        <div class="mt-8 flex items-center space-x-4">
            <p class="text-gray-600">
                {{ __('pagination.showing', ['first' => $inquiries->firstItem(), 'last' => $inquiries->lastItem(), 'total' => $inquiries->total()]) }}
            </p>
            {{ $inquiries->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

@endsection