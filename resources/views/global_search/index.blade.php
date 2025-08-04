@extends('layouts.app')

@section('content')
    <div class="w-full p-6">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">名称一括検索</h2>

        <!-- 🔍 検索フォーム -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <form method="GET" action="{{ route('global_search.index') }}">
                @if (!empty($error))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ $error }}
                    </div>
                @endif

                <p class="text-sm text-gray-600 mb-4">
                    クライアント名・取引責任者名・関係者名・関係者の担当者名を対象に検索します。
                </p>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-gray-700 font-semibold">名称（漢字 or かな）</label>
                        <input type="text" name="keyword" value="{{ request('keyword') }}"
                            class="w-full border px-3 py-2 rounded" placeholder="例：山田太郎、やまだたろう">
                    </div>
                </div>

                <div class="mt-4 flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">検索</button>
                    <a href="{{ route('global_search.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">条件クリア</a>
                </div>
            </form>
        </div>

        <!-- 🔽 検索結果セクション：4分類 -->
        @if (isset($keyword))
            <div class="bg-white p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">検索結果（キーワード：{{ $keyword }}）</h3>
            
                <!-- 件数サマリ -->
                <div class="text-sm text-gray-700 mb-6">
                    クライアント名：{{ $clientsByName->count() }}件 ／
                    取引責任者名：{{ $clientsByContact->count() }}件 ／
                    関係者名：{{ $relatedByName->count() }}件 ／
                    関係者の担当者名：{{ $relatedByManager->count() }}件
                </div>
            
                <!-- クライアント名 -->
                <h4 class="font-semibold text-blue-800 mb-2">クライアント名でヒット（{{ $clientsByName->count() }}件）</h4>
                @if ($clientsByName->isEmpty())
                    <p class="text-gray-500 mb-4">該当するクライアントはありません。</p>
                @else
                    <div class="overflow-x-auto mb-6">
                        <table class="w-full table-auto text-sm border border-gray-300">
                            <thead class="bg-sky-700 text-white">
                                <tr>
                                    <th class="border px-2 py-1">クライアント名（漢字）</th>
                                    <th class="border px-2 py-1">クライアント名（かな）</th>
                                    <th class="border px-2 py-1">種別</th>
                                    <th class="border px-2 py-1">詳細</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientsByName as $client)
                                    <tr>
                                        <td class="border px-2 py-1">{{ $client->name_kanji }}</td>
                                        <td class="border px-2 py-1">{{ $client->name_kana }}</td>
                                        <td class="border px-2 py-1">{{ config('master.client_types')[$client->client_type] ?? '-' }}</td>
                                        <td class="border px-2 py-1">
                                            <a href="{{ route('client.show', $client->id) }}" class="text-blue-600 underline">詳細</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <!-- 責任者名 -->
                <h4 class="font-semibold text-blue-800 mb-2">取引責任者名でヒット（{{ $clientsByContact->count() }}件）</h4>
                @if ($clientsByContact->isEmpty())
                    <p class="text-gray-500 mb-4">該当する取引責任者はありません。</p>
                @else
                    <div class="overflow-x-auto mb-6">
                        <table class="w-full table-auto text-sm border border-gray-300">
                            <thead class="bg-sky-700 text-white">
                                <tr>
                                    <th class="border px-2 py-1">クライアント名（漢字）</th>
                                    <th class="border px-2 py-1">クライアント名（かな）</th>
                                    <th class="border px-2 py-1">取引責任者名（漢字）</th>
                                    <th class="border px-2 py-1">取引責任者名（かな）</th>
                                    <th class="border px-2 py-1">種別</th>
                                    <th class="border px-2 py-1">詳細</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientsByContact as $client)
                                    <tr>
                                        <td class="border px-2 py-1">{{ $client->name_kanji }}</td>
                                        <td class="border px-2 py-1">{{ $client->name_kana }}</td>
                                        <td class="border px-2 py-1">{{ $client->contact_last_name_kanji . $client->contact_first_name_kanji }}</td>
                                        <td class="border px-2 py-1">{{ $client->contact_last_name_kana . $client->contact_first_name_kana }}</td>
                                        <td class="border px-2 py-1">{{ config('master.client_types')[$client->client_type] ?? '-' }}</td>
                                        <td class="border px-2 py-1">
                                            <a href="{{ route('client.show', $client->id) }}" class="text-blue-600 underline">詳細</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <!-- 関係者名 -->
                <h4 class="font-semibold text-blue-800 mb-2">関係者名でヒット（{{ $relatedByName->count() }}件）</h4>
                @if ($relatedByName->isEmpty())
                    <p class="text-gray-500 mb-4">該当する関係者はありません。</p>
                @else
                    <div class="overflow-x-auto mb-6">
                        <table class="w-full table-auto text-sm border border-gray-300">
                            <thead class="bg-sky-700 text-white">
                                <tr>
                                    <th class="border px-2 py-1">氏名（漢字）</th>
                                    <th class="border px-2 py-1">氏名（かな）</th>
                                    <th class="border px-2 py-1">区分</th>
                                    <th class="border px-2 py-1">分類</th>
                                    <th class="border px-2 py-1">詳細</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($relatedByName as $r)
                                    <tr>
                                        <td class="border px-2 py-1">{{ $r->relatedparties_name_kanji }}</td>
                                        <td class="border px-2 py-1">{{ $r->relatedparties_name_kana }}</td>
                                        <td class="border px-2 py-1">{{ config('master.relatedparties_parties')[$r->relatedparties_party] ?? '-' }}</td>
                                        <td class="border px-2 py-1">{{ config('master.relatedparties_classes')[$r->relatedparties_class] ?? '-' }}</td>
                                        <td class="border px-2 py-1">
                                            <a href="{{ route('relatedparty.show', $r->id) }}" class="text-blue-600 underline">詳細</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
                <!-- 担当者名 -->
                <h4 class="font-semibold text-blue-800 mb-2">関係者の担当者名でヒット（{{ $relatedByManager->count() }}件）</h4>
                @if ($relatedByManager->isEmpty())
                    <p class="text-gray-500">該当する関係者の担当者はありません。</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto text-sm border border-gray-300">
                            <thead class="bg-sky-700 text-white">
                                <tr>
                                    <th class="border px-2 py-1">関係者名（漢字）</th>
                                    <th class="border px-2 py-1">関係者名（かな）</th>
                                    <th class="border px-2 py-1">区分</th>
                                    <th class="border px-2 py-1">分類</th>
                                    <th class="border px-2 py-1">担当者名（漢字）</th>
                                    <th class="border px-2 py-1">担当者名（かな）</th>
                                    <th class="border px-2 py-1">詳細</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($relatedByManager as $r)
                                    <tr>
                                        <td class="border px-2 py-1">{{ $r->relatedparties_name_kanji }}</td>
                                        <td class="border px-2 py-1">{{ $r->relatedparties_name_kana }}</td>
                                        <td class="border px-2 py-1">{{ config('master.relatedparties_parties')[$r->relatedparties_party] ?? '-' }}</td>
                                        <td class="border px-2 py-1">{{ config('master.relatedparties_classes')[$r->relatedparties_class] ?? '-' }}</td>
                                        <td class="border px-2 py-1">{{ $r->manager_name_kanji }}</td>
                                        <td class="border px-2 py-1">{{ $r->manager_name_kana }}</td>
                                        <td class="border px-2 py-1">
                                            <a href="{{ route('relatedparty.show', $r->id) }}" class="text-blue-600 underline">詳細</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection