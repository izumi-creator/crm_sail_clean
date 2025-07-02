@extends('layouts.app')

@section('content')
<div class="min-h-[60vh] flex flex-col items-center justify-center bg-white px-6 py-12">
    <div class="text-center border border-red-300 bg-red-50 p-8 rounded shadow-md max-w-xl w-full">
        <h1 class="text-2xl font-bold text-red-700 mb-4">データベースでエラーが発生しました</h1>
        <p class="text-gray-700 mb-6">{{ $message ?? '不明なエラーです。' }}</p>
        <a href="{{ url()->previous() }}"
           class="inline-block px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            戻る
        </a>
    </div>
</div>
@endsection