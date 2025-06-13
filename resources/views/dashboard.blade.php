@extends('layouts.app')

@section('content')
<div class="w-full p-6">
    <h1 class="text-2xl font-bold mb-4">ダッシュボード</h1>

    @php
    $tasks = [
        ['title' => 'ABC株式会社の初回相談：長文の場合長文の場合長文の場合長文の場合長文の場合長文の場合', 'assignee' => '田中 テスト', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤 テスト', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木 テスト', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中 テスト', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤 テスト', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木 テスト', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        ['title' => 'ABC株式会社の初回相談', 'assignee' => '田中', 'deadline' => '2025-03-20', 'status' => '未着手'],
        ['title' => '個人〇〇様の初回相談', 'assignee' => '佐藤', 'deadline' => '2025-03-25', 'status' => '未着手'],
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手']
    ];
    $cases = [
            ['name' => 'ABC株式〇〇事件', 'client' => 'ABC株式会社', 'assignee' => '田中', 'progress' => '介入前'],
            ['name' => 'CRMサイト移行', 'client' => 'XYZ商事', 'assignee' => '佐藤', 'progress' => '対応中'],
            ['name' => '〇〇株式会社の不当解雇', 'client' => 'テスト 太郎', 'assignee' => '鈴木', 'progress' => '対応中'],
            ['name' => '〇〇株式会社への残業代請求', 'client' => 'テスト 花子', 'assignee' => '鈴木', 'progress' => '対応後処理中']
        ];
    @endphp

    <!-- 相談予定 -->
    <div class="mb-10">
        <h2 class="text-xl font-semibold mb-4">相談予定（{{ count($tasks) }}件）</h2>
        <div class="p-4 border rounded-lg shadow bg-white">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="sticky top-0 bg-sky-700 text-white z-10 text-sm shadow-md border-b border-gray-300">
                        <tr>
                            <th class="border p-2 w-5/12">タイトル</th>
                            <th class="border p-2 w-3/12">担当者</th>
                            <th class="border p-2 w-2/12">期限</th>
                            <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($tasks as $task)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $task['title'] }}</td>
                            <td class="border px-2 py-[6px] truncate">{{ $task['assignee'] }}</td>
                            <td class="border px-2 py-[6px] truncate">{{ $task['deadline'] }}</td>
                            <td class="border px-2 py-[6px] truncate">{{ $task['status'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 受任案件一覧 -->
    <div class="mb-10">
        <h2 class="text-xl font-semibold mb-4">受任案件一覧（{{ count($cases) }}件）</h2>
        <div class="p-4 border rounded-lg shadow bg-white">
            <div class="overflow-y-auto max-h-48">
                <table class="w-full border-collapse border border-gray-300 table-fixed">
                    <thead class="sticky top-0 bg-sky-700 text-white z-10 text-sm shadow-md border-b border-gray-300">
                        <tr>
                            <th class="border p-2 w-5/12">案件名</th>
                            <th class="border p-2 w-3/12">クライアント</th>
                            <th class="border p-2 w-2/12">担当者</th>
                            <th class="border p-2 w-2/12">ステータス</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($cases as $case)
                        <tr>
                            <td class="border px-2 py-[6px] truncate">{{ $case['name'] }}</td>
                            <td class="border px-2 py-[6px] truncate">{{ $case['client'] }}</td>
                            <td class="border px-2 py-[6px] truncate">{{ $case['assignee'] }}</td>
                            <td class="border px-2 py-[6px] truncate">{{ $case['progress'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- <div class="space-y-2">
        {{-- ローカルフォルダをエクスプローラーで開く --}}
        <div>
            <a href="file:///C:/Users/User/Box/test/" class="localexplorer" target="_blank">📂 エクスプローラーでフォルダ開く</a>
            <button onclick="copyToClipboard('file:///C:/Users/User/Box/test/')" class="text-blue-500">コピー</button>
        </div>
    
        {{-- ローカルファイルをエクスプローラーで開く --}}
        <div>
            <a href="file:///C:/Users/User/Box/test/test3.txt" class="localexplorer" target="_blank">📂 エクスプローラーでファイル開く</a>
            <button onclick="copyToClipboard('file:///C:/Users/User/Box/test/test3.txt')" class="text-blue-500">コピー</button>
        </div>
    
        {{-- Box WebフォルダURL --}}
        <div>
            <a href="https://app.box.com/folder/314075644618" target="_blank">📂 Box Webフォルダ</a>
            <button onclick="copyToClipboard('https://app.box.com/folder/314075644618')" class="text-blue-500">コピー</button>
        </div>
    
        {{-- Box フォルダIDによるWebリンク --}}
        <div>
            <a href="https://app.box.com/folder/314075644618" target="_blank">📂 Box WebフォルダID</a>
            <button onclick="copyToClipboard('https://app.box.com/folder/314075644618')" class="text-blue-500">コピー</button>
        </div>
    </div>
    
    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('リンクをコピーしました！');
        }).catch(err => {
            alert('コピーに失敗しました：' + err);
        });
    }
    </script> -->
</div>
@endsection
