@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6">
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
        ['title' => 'XYZ株式会社の顧問契約相談', 'assignee' => '鈴木', 'deadline' => '2025-03-22', 'status' => '未着手'],
        
    ];
    $cases = [
        ['name' => '〇〇事件', 'client' => 'ABC株式会社', 'progress' => '50%', 'assignee' => '田中'],
        ['name' => '従業員〇〇の△△事件対応：長文の場合長文の場合長文の場合長文の場合長文の場合長文の場合', 'client' => 'XYZ商事', 'progress' => '30%', 'assignee' => '佐藤'],
        ['name' => '〇〇株式会社による不当解雇', 'client' => '個人 太郎', 'progress' => '80%', 'assignee' => '鈴木'],
    ];
    @endphp

    <!-- 相談一覧 -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2">相談予定（{{ count($tasks) }}件）</h2>
        <div class="p-4 border rounded-lg shadow bg-white h-48 overflow-y-auto whitespace-nowrap">
            <table class="w-full border-collapse border border-gray-300 table-fixed">
                <thead>
                    <tr class="bg-sky-700 text-white">
                        <th class="border p-1 w-5/12">タイトル</th>
                        <th class="border p-1 w-3/12">担当者</th>
                        <th class="border p-1 w-2/12">期限</th>
                        <th class="border p-1 w-2/12">ステータス</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                    <tr>
                        <td class="border p-1 truncate">{{ $task['title'] }}</td>
                        <td class="border p-1 truncate">{{ $task['assignee'] }}</td>
                        <td class="border p-1 truncate">{{ $task['deadline'] }}</td>
                        <td class="border p-1 truncate">{{ $task['status'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- 案件一覧 -->
    <div>
        <h2 class="text-xl font-semibold mb-2">未完了の受任案件（{{ count($cases) }}件）</h2>
        <div class="p-4 border rounded-lg shadow bg-white h-48 overflow-y-auto whitespace-nowrap">
            <table class="w-full border-collapse border border-gray-300 table-fixed">
                <thead>
                    <tr class="bg-sky-700 text-white">
                        <th class="border p-1 w-5/12">案件名</th>
                        <th class="border p-1 w-3/12">クライアント</th>
                        <th class="border p-1 w-2/12">進捗状況</th>
                        <th class="border p-1 w-2/12">担当者</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cases as $case)
                    <tr>
                        <td class="border p-1 truncate">{{ $case['name'] }}</td>
                        <td class="border p-1 truncate">{{ $case['client'] }}</td>
                        <td class="border p-1 truncate">{{ $case['progress'] }}</td>
                        <td class="border p-1 truncate">{{ $case['assignee'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
