@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full p-6"> <!-- ✅ ダッシュボードと統一 -->
        <h2 class="text-2xl font-bold mb-4 text-gray-800">クライアント一覧</h2>

        <!-- 🔍 検索フォーム -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <form method="GET" action="{{ route('client.index') }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold">クライアント名（漢字）</label>
                        <input type="text" name="name_kanji" value="{{ request('name_kanji') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">クライアント名（ふりがな）</label>
                        <input type="text" name="name_kana" value="{{ request('name_kana') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">電話番号：関連項目</label>
                        <input type="text" name="phone_number_search" value="{{ request('phone_number_search') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">メールアドレス：関連項目</label>
                        <input type="text" name="email_search" value="{{ request('email_search') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>

                </div>

                <!-- ボタンエリア -->
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded min-w-[100px]">検索</button>
                    <a href="{{ route('client.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">検索条件クリア</a>
                    <a href="{{ route('client.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
                </div>
            </form>
        </div>

<!-- 📋 検索結果（ダッシュボードと同じ設定 + ページネーション内包） -->
<div class="mb-10">
    <h2 class="text-xl font-semibold mb-4">検索結果（{{ $clients->total() }}件）</h2>
    <div class="p-4 border rounded-lg shadow bg-white">
        <div class="overflow-y-auto max-h-48"> <!-- ✅ 高さをダッシュボードと統一 -->
            <table class="w-full border-collapse border border-gray-300 table-fixed">
                <thead class="sticky top-0 bg-sky-700 text-white z-10 text-sm shadow-md border-b border-gray-300">
                    <tr>
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-6/12">クライアント名（漢字）</th>
                        <th class="border p-2 w-5/12">クライアント名（かな）</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($clients as $client)
                    <tr>
                        <td class="border px-2 py-[6px] truncate">{{ $client->id }}</td>
                        <td class="border px-2 py-[6px] truncate">
                            <a href="{{ route('client.show', $client->id) }}" class="text-blue-500">
                                {{ $client->name_kanji }}
                            </a>
                        </td>
                        <td class="border px-2 py-[6px] truncate">
                            {{ $client->name_kana }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ✅ ページネーションを検索結果カード内に配置 -->
        <div class="mt-8 flex items-center space-x-4">
            <p class="text-gray-600">
                {{ __('pagination.showing', ['first' => $clients->firstItem(), 'last' => $clients->lastItem(), 'total' => $clients->total()]) }}
            </p>
            {{ $clients->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

@endsection
