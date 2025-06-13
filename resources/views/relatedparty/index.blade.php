@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="w-full p-6"> <!-- ✅ ダッシュボードと統一 -->
        <h2 class="text-2xl font-bold mb-4 text-gray-800">関係者管理</h2>

        <!-- 🔍 検索フォーム -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <form method="GET" action="{{ route('relatedparty.index') }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold">関係者名（漢字）</label>
                        <input type="text" name="relatedparties_name_kanji" value="{{ request('relatedparties_name_kanji') }}" 
                            class="w-full border px-3 py-2 rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">区分</label>
                        <select name="relatedparties_party" class="w-full border px-3 py-2 rounded">
                            <option value="">選択してください</option>
                            @foreach (config('master.relatedparties_parties') as $key => $label)
                                <option value="{{ $key }}" {{ request('relatedparties_party') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">分類</label>
                        <select name="relatedparties_class" class="w-full border px-3 py-2 rounded">
                            <option value="">選択してください</option>
                            @foreach (config('master.relatedparties_classes') as $key => $label)
                                <option value="{{ $key }}" {{ request('relatedparties_class') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-semibold">種別</label>
                        <select name="relatedparties_type" class="w-full border px-3 py-2 rounded">
                            <option value="">選択してください</option>
                            @foreach (config('master.relatedparties_types') as $key => $label)
                                <option value="{{ $key }}" {{ request('relatedparties_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- ボタンエリア -->
                <div class="mt-4 flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded min-w-[100px]">検索</button>
                    <a href="{{ route('relatedparty.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">検索条件クリア</a>
                    <a href="{{ route('relatedparty.create') }}" class="bg-green-500 text-white px-4 py-2 rounded">新規登録</a>
                </div>
            </form>
        </div>

<!-- 📋 検索結果（ダッシュボードと同じ設定 + ページネーション内包） -->
<div class="mb-10">
    <h2 class="text-xl font-semibold mb-4">検索結果（{{ $relatedparties->total() }}件）</h2>
    <div class="p-4 border rounded-lg shadow bg-white">
        <div class="overflow-y-auto max-h-48"> <!-- ✅ 高さをダッシュボードと統一 -->
            <table class="w-full border-collapse border border-gray-300 table-fixed">
                <thead class="sticky top-0 bg-sky-700 text-white z-10 text-sm shadow-md border-b border-gray-300">
                    <tr>
                        <th class="border p-2 w-1/12">ID</th>
                        <th class="border p-2 w-4/12">関係者名（漢字）</th>
                        <th class="border p-2 w-2/12">区分</th>
                        <th class="border p-2 w-2/12">分類</th>
                        <th class="border p-2 w-2/12">種別</th>
                        <th class="border p-2 w-3/12">立場</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach ($relatedparties as $relatedparty)
                    <tr>
                        <td class="border px-2 py-[6px] truncate">{{ $relatedparty->id }}</td>
                        <td class="border px-2 py-[6px] truncate">
                            <a href="{{ route('relatedparty.show', $relatedparty->id) }}" class="text-blue-500">
                                {{ $relatedparty->relatedparties_name_kanji }}
                            </a>
                        </td>
                        <td class="border px-2 py-[6px] truncate">
                            {{ config('master.relatedparties_parties')[(string)$relatedparty->relatedparties_party] ?? '未設定' }}
                        </td>
                        <td class="border px-2 py-[6px] truncate">
                            {{ config('master.relatedparties_classes')[(string)$relatedparty->relatedparties_class] ?? '未設定' }}
                        </td>
                        <td class="border px-2 py-[6px] truncate">
                            {{ config('master.relatedparties_types')[(string)$relatedparty->relatedparties_type] ?? '未設定' }}
                        </td>
                        <td class="border px-2 py-[6px] truncate">
                            {{ config('master.relatedparties_positions')[(string)$relatedparty->relatedparties_position] ?? '未設定' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ✅ ページネーションを検索結果カード内に配置 -->
        <div class="mt-8 flex items-center space-x-4">
            <p class="text-gray-600">
                {{ __('pagination.showing', ['first' => $relatedparties->firstItem(), 'last' => $relatedparties->lastItem(), 'total' => $relatedparties->total()]) }}
            </p>
            {{ $relatedparties->links('vendor.pagination.custom') }}
        </div>
    </div>
</div>

@endsection
