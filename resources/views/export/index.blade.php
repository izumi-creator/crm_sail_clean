@extends('layouts.app')

@section('content')
<!-- 外枠カード（白背景 + 丸みあり） -->
<div class="max-w-5xl mx-auto mt-10 bg-white shadow-lg p-8 rounded-lg">
    <!-- ヘッダー -->
    <div class="bg-sky-700 text-white px-4 py-2 font-bold border-b">
        データダウンロード
    </div>

    <!-- フォーム全体（囲み）※丸みなし -->
    <div class="p-6 border border-gray-300 border-t-0 text-sm">
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-600 rounded">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <form id="download-form">
            <div class="mb-6">
                <label class="block mb-1 font-semibold" for="type">データ種別</label>
                <select name="type" id="type"
                        class="w-full border border-gray-300 px-3 py-2 rounded focus:outline-none focus:ring focus:border-blue-300"
                        required>
                    <option value="">＜データ選択＞</option>
                    <option value="clients">クライアント</option>
                    <option value="inquiries">問合せ</option>
                    <option value="consultations">相談</option>
                    <option value="businesses">受任案件</option>
                    <option value="advisory_contracts">顧問契約</option>
                    <option value="advisory_consultations">顧問相談</option>
                    <option value="related_parties">関係者</option>
                    <option value="tasks">タスク</option>
                    <option value="negotiations">折衝履歴</option>
                    <option value="court_tasks">裁判所タスク</option>
                    <option value="insurances">保険会社マスタ</option>
                    <option value="courts">裁判所マスタ</option>
                    <option value="users">スタッフ</option>
                    <option value="rooms">施設</option>
                </select>
            </div>
        
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 min-w-[120px]">
                    ダウンロード
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<!-- ✅ ダウンロード開始通知トースト -->
<div id="download-toast" class="fixed bottom-4 right-4 bg-blue-600 text-white px-4 py-3 rounded-lg shadow-lg hidden z-50 text-sm">
    ✅ ダウンロード処理を開始します。<br>
    ブラウザの設定によっては、保存先の選択ダイアログが表示されます。<br>
    保存が完了しましたら、データが保存されていることをご確認ください。<br>
    （このメッセージは5秒後に自動で閉じます）
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('download-form');
        const toast = document.getElementById('download-toast');
    
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // 通常の送信をブロック
    
            const formData = new FormData(form);
            const type = formData.get('type');
    
            if (!type) return alert('データ種別を選択してください');
    
            // ✅ トースト表示
            toast.classList.remove('hidden');
            toast.classList.add('flex', 'flex-col');
    
            setTimeout(() => {
                toast.classList.remove('flex', 'flex-col');
                toast.classList.add('hidden');
            }, 5000);
    
            // ✅ 非同期でCSV取得
            fetch("{{ route('export.download') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: formData
            })
            .then(response => {
                const disposition = response.headers.get('Content-Disposition');
                let filename = 'download.csv';

                if (disposition && disposition.indexOf('filename=') !== -1) {
                    const matches = disposition.match(/filename="?(.+)"?/);
                    if (matches && matches[1]) {
                        filename = matches[1];
                    }
                }

                return response.blob().then(blob => ({ blob, filename }));
            })
            .then(({ blob, filename }) => {
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = filename;
                document.body.appendChild(link);
                link.click();
                link.remove();
            })
            .catch(err => {
                alert('ダウンロードに失敗しました');
                console.error(err);
            });
        });
    });
</script>
