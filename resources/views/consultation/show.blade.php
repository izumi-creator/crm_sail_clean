@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold mb-4 text-gray-800">相談詳細</h2>

    <!-- ✅ 上段：主要項目カード（個人／法人で出し分け） -->
    <div class="border rounded-lg shadow bg-white mb-6 overflow-hidden">
        <!-- 見出しバー -->
        <div class="bg-sky-700 text-white px-6 py-3 border-b border-sky-800">
            <div class="text-sm text-gray-100 mb-1">
                {{ $consultation->consultation_party == 1 ? '個人の相談' : '法人の相談' }}
            </div>
            <div class="text-xl font-bold">
                {{ $consultation->client->name_kanji }}（{{ $consultation->client->name_kana }}）
            </div>
        </div>

        <!-- 内容エリア -->
        <div class="grid grid-cols-2 gap-4 text-sm text-gray-700 px-6 py-4">
            @if ($consultation->consultation_party == 1)
                <!-- 個人クライアント用表示 -->
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! $consultation->client->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! $consultation->client->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス1:</span>
                    <span class="ml-2">{!! $consultation->client->email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">メールアドレス2:</span>
                    <span class="ml-2">{!! $consultation->client->email2 ?: '&nbsp;' !!}</span>
                </div>
            @else
                <!-- 法人クライアント用表示 -->
                <div class="col-span-2">
                    <span class="font-semibold">取引先責任者名:</span>
                    <span class="ml-2">
                        {{ $consultation->client->contact_last_name_kanji }}　{{ $consultation->client->contact_first_name_kanji }}
                        （{{ $consultation->client->contact_last_name_kana }}　{{ $consultation->client->contact_first_name_kana }}）
                    </span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第一連絡先）:</span>
                    <span class="ml-2">{!! $consultation->client->first_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">電話番号（第二連絡先）:</span>
                    <span class="ml-2">{!! $consultation->client->second_contact_number ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール1:</span>
                    <span class="ml-2">{!! $consultation->client->contact_email1 ?: '&nbsp;' !!}</span>
                </div>
                <div>
                    <span class="font-semibold">取引先責任者_メール2:</span>
                    <span class="ml-2">{!! $consultation->client->contact_email2 ?: '&nbsp;' !!}</span>
                </div>
            @endif
        </div>
    </div>

    <!-- タブ切替ボタン -->
    <div class="mb-0 overflow-x-auto border-b border-gray-300 bg-gray-100 rounded-t">
        <div class="flex space-x-2 pt-2 px-6 w-fit">
            <button class="tab-btn active-tab px-4 py-2 text-sm font-bold text-sky-700 bg-white border-x border-t border-b-0 rounded-t" data-tab="tab-detail">
                詳細情報
            </button>
            <button class="tab-btn px-4 py-2 text-sm text-gray-700 hover:bg-gray-200 rounded-t" data-tab="tab-relatedparty">
                関係者一覧（3件）
            </button>
        </div>
    </div>


    <!-- ▼ 詳細情報タブ（今ある内容を全部この中に入れる） -->
    <div id="tab-detail" class="tab-content">

        <!-- 相談詳細カード -->
        <div class="p-6 border rounded-lg shadow bg-white">
            <!-- 上部ボタン -->
            <div class="flex justify-end space-x-2 mb-4">
                <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="bg-amber-500 hover:bg-amber-600 text-black px-4 py-2 rounded min-w-[100px]">編集</button>
                @if (auth()->user()->role_type == 1)
                <button onclick="document.getElementById('deleteModal').classList.remove('hidden')" class="bg-red-500 hover:bg-red-600 text-black px-4 py-2 rounded min-w-[100px]">削除</button>
                @endif
            </div>

            <!-- ✅ 相談情報の見出し＋内容を枠で囲む -->
            <div class="border border-gray-300 overflow-hidden">
                <!-- 見出し -->
                <div class="bg-sky-700 text-white px-4 py-2 font-bold border">相談情報</div>
                <!-- 内容 -->
                <div class="grid grid-cols-2 gap-6 pt-0 pb-6 px-6 text-sm">
                    <div class="col-span-2 bg-blue-100 text-blue-900 font-semibold py-2 px-6 -mx-6">
                        基本情報
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">件名</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->title ?: '&nbsp;' !!}</div>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">事件概要</label>
                        <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->case_summary ?: '&nbsp;' !!}</div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>サンプル（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div><label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->special_notes ?: '&nbsp;' !!}</div>
                                </div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->special_notes ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-2 mt-2 -mx-6">
                        <div class="flex items-center justify-between bg-blue-100 text-blue-900 font-semibold py-2 px-6 cursor-pointer accordion-toggle">
                            <span>関係先（クリックで開閉）</span>
                            <i class="fa-solid fa-chevron-down transition-transform duration-300 accordion-icon"></i>
                        </div>
                        <div class="accordion-content hidden pt-4 px-6">
                            <div class="grid grid-cols-2 gap-6">
                                <div><label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->special_notes ?: '&nbsp;' !!}</div>
                                </div>
                                <div><label class="block text-sm font-semibold text-gray-700 mb-1">特記事項</label>
                                    <div class="mt-1 p-2 border rounded bg-gray-50">{!! $consultation->special_notes ?: '&nbsp;' !!}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ✅ 外枠の外に表示 -->
            <div class="relative mt-6 h-10">
               <!-- 左側：一覧に戻る -->
                <div class="absolute left-0">
                    <a href="{{ route('consultation.index') }}" class="text-blue-600 hover:underline hover:text-blue-800">一覧に戻る</a>
                </div>
            </div>
        </div>
    </div>

    <!-- ▼ 関係者一覧タブ -->
    <div id="tab-relatedparty" class="tab-content hidden">
        <div class="p-6 border rounded-lg shadow bg-white text-gray-700">
            <p>関係者一覧の内容（今はダミー）</p>
        </div>
    </div>


    <!-- 削除モーダル -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white shadow-lg w-full max-w-md">
            <!-- ヘッダー -->
            <div class="bg-red-600 text-white px-4 py-2 font-bold border-b">相談削除</div>

            <!-- 本文 -->
            <div class="px-6 py-4 text-sm">
                <p class="mb-2">本当にこの相談を削除しますか？</p>
                <p class="mb-2">この操作は取り消せません。</p>
            </div>

            <!-- フッター -->
            <form method="POST" action="{{ route('consultation.destroy', $consultation->id) }}">
                @csrf
                @method('DELETE')
                <div class="flex justify-end space-x-2 px-6 pb-6">
                    <button type="button" onclick="document.getElementById('deleteModal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-300 text-black rounded">
                        キャンセル
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 min-w-[100px]">
                        削除
                    </button>
                </div>
            </form>
        </div>
    </div>
            
@endsection

@section('scripts')

@if ($errors->any())
<script>
    window.addEventListener('load', function () {
        document.getElementById('editModal')?.classList.remove('hidden');
        document.querySelectorAll('.accordion-content').forEach(content => {
            content.classList.remove('hidden');
            const icon = content.previousElementSibling?.querySelector('.accordion-icon');
            icon?.classList.add('rotate-180');
        });
    });
</script>
@endif

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ▽ 0. モーダル表示
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const selectedTab = btn.dataset.tab;

            // ボタンのクラス切り替え
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove(
                    'bg-white', 'text-sky-700', 'font-bold', 'border-x', 'border-t', 'border-b-0'
                );
                b.classList.add('text-gray-700');
            });

            btn.classList.add(
                'bg-white', 'text-sky-700', 'font-bold', 'border-x', 'border-t', 'border-b-0'
            );
            btn.classList.remove('text-gray-700');

            // コンテンツ切り替え
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(selectedTab)?.classList.remove('hidden');
        });
    });

    // ▽ 1. アコーディオン制御  
    const toggles = document.querySelectorAll('.accordion-toggle');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function () {
            const content = this.nextElementSibling;
            const icon = this.querySelector('.accordion-icon');
            if (content && content.classList.contains('accordion-content')) {
                content.classList.toggle('hidden');
                icon?.classList.toggle('rotate-180');
            }
        });
    });
});
</script>
@endsection