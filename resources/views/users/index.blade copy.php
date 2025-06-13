@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
<div class="container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-4">スタッフ一覧</h2>

    <!-- 検索エリア -->
    <form method="GET" action="{{ route('users.index') }}" class="mb-4">
        <input type="text" name="name" placeholder="名前" value="{{ request('name') }}" class="border p-2">
        <input type="email" name="email" placeholder="メールアドレス" value="{{ request('email') }}" class="border p-2">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2">検索</button>
        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2">条件クリア</a>
        <a href="{{ route('users.create') }}" class="bg-green-500 text-white px-4 py-2">スタッフ追加</a>
    </form>

    <!-- スタッフ一覧 -->
    <table class="min-w-full bg-white shadow-md rounded my-6">
        <thead>
            <tr class="bg-gray-200 text-gray-700">
                <th class="py-2 px-4">氏名</th>
                <th class="py-2 px-4">メールアドレス</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr class="border-t">
                    <td class="py-2 px-4">
                        <a href="{{ route('users.show', $user->id) }}" class="text-blue-500">{{ $user->name }}</a>
                    </td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ページネーション -->
    <div class="mt-4">
        {{ $users->links() }}
    </div>
</div>
@endsection