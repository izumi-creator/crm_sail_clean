<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CRMシステム') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        function toggleMenu() {
            let sidebar = document.getElementById("sidebar");
            let menuButton = document.getElementById("menuButton");

            let isClosed = sidebar.classList.toggle("closed");

            if (isClosed) {
                sidebar.style.width = "0px";
                menuButton.style.position = "fixed";
                menuButton.style.left = "15px";
                menuButton.style.right = "auto";
                menuButton.style.top = "20px";
                menuButton.style.zIndex = "9999";
                menuButton.style.visibility = "visible";
            } else {
                sidebar.style.width = "256px";
                menuButton.style.position = "absolute";
                menuButton.style.right = "10px";
                menuButton.style.left = "auto";
                menuButton.style.top = "50%";
                menuButton.style.transform = "translateY(-50%)";
            }

            menuButton.style.display = "block";
        }

        document.addEventListener('DOMContentLoaded', function () {
            // ▼ ユーザー（担当弁護士・パラリーガルなど）
            $('.select-user').each(function () {
                const $select = $(this);
                const oldId = $select.data('old-id');
                const oldText = $select.data('old-text');
            
                $select.select2({
                    width: '100%',
                    minimumInputLength: 1,
                    allowClear: true,
                    placeholder: 'ユーザーを検索してください',
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '該当するユーザーは見つかりません'
                    },
                    ajax: {
                        url: '{{ route("users.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                    }
                });
            
                if (oldId && oldText) {
                    const option = new Option(oldText, oldId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ クライアント
            $('.select-client').each(function () {
                const $select = $(this);
                const oldId = $select.data('old-id');
                const oldText = $select.data('old-text');
            
                $select.select2({
                    width: '100%',
                    minimumInputLength: 1,
                    allowClear: true,
                    placeholder: 'クライアントを検索してください',
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '該当するクライアントは見つかりません'
                    },
                    ajax: {
                        url: '{{ route("client.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                    }
                });
            
                if (oldId && oldText) {
                    const option = new Option(oldText, oldId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ 相談
            $('.select-consultation').each(function () {
                const $select = $(this);
                const oldId = $select.data('old-id');
                const oldText = $select.data('old-text');
            
                $select.select2({
                    width: '100%',
                    minimumInputLength: 1,
                    allowClear: true,
                    placeholder: '相談を検索してください',
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する相談は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("consultations.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                    }
                });
            
                if (oldId && oldText) {
                    const option = new Option(oldText, oldId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ 受任案件
            $('.select-business').each(function () {
                const $select = $(this);
                const oldId = $select.data('old-id');
                const oldText = $select.data('old-text');
            
                $select.select2({
                    width: '100%',
                    minimumInputLength: 1,
                    allowClear: true,
                    placeholder: '受任案件を検索してください',
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する受任案件は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("businesses.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                    }
                });
            
                if (oldId && oldText) {
                    const option = new Option(oldText, oldId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ 裁判所
            $('.select-court').each(function () {
                const $select = $(this);
                const oldId = $select.data('old-id');
                const oldText = $select.data('old-text');
            
                $select.select2({
                    width: '100%',
                    minimumInputLength: 1,
                    allowClear: true,
                    placeholder: '裁判所を検索してください',
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する裁判所は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("courts.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                    }
                });
            
                if (oldId && oldText) {
                    const option = new Option(oldText, oldId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ 顧問契約
            $('.select-advisory').each(function () {
                const $select = $(this);
                const oldId = $select.data('old-id');
                const oldText = $select.data('old-text');
            
                $select.select2({
                    width: '100%',
                    minimumInputLength: 1,
                    allowClear: true,
                    placeholder: '顧問契約を検索してください',
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する顧問契約は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("advisory.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                    }
                });
            
                if (oldId && oldText) {
                    const option = new Option(oldText, oldId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ 顧問相談
            $('.select-advisory-consultation').each(function () {
                const $select = $(this);
                const oldId = $select.data('old-id');
                const oldText = $select.data('old-text');
            
                $select.select2({
                    width: '100%',
                    minimumInputLength: 1,
                    allowClear: true,
                    placeholder: '顧問相談を検索してください',
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する顧問相談は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("advisory_consultation.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results }),
                    }
                });
            
                if (oldId && oldText) {
                    const option = new Option(oldText, oldId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ ユーザー（担当弁護士・パラリーガルなど）
            $('.select-user-edit').each(function () {
                const $select = $(this);
                const initialId = $select.data('initial-id');
                const initialText = $select.data('initial-text');
            
                $select.select2({
                    width: '100%', // ← width: resolve → 100% に明示修正
                    dropdownParent: $('#editModal'), // ← モーダル内でdropdownがずれるのを防止
                    placeholder: 'ユーザーを検索',
                    minimumInputLength: 1,
                    allowClear: true,
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致するユーザーは見つかりません'
                    },
                    ajax: {
                        url: '{{ route("users.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results })
                    }
                });
            
                if (initialId && initialText) {
                    const option = new Option(initialText, initialId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ クライアント
            $('.select-client-edit').each(function () {
                const $select = $(this);
                const initialId = $select.data('initial-id');
                const initialText = $select.data('initial-text');
            
                $select.select2({
                    width: '100%',
                    placeholder: 'クライアントを検索',
                    minimumInputLength: 1,
                    allowClear: true,
                    dropdownParent: $('#editModal'), // ← モーダル内なら必要、通常画面なら削除OK
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致するクライアントは見つかりません'
                    },
                    ajax: {
                        url: '{{ route("client.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results })
                    }
                });
            
                if (initialId && initialText) {
                    const option = new Option(initialText, initialId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ 相談
            $('.select-consultation-edit').each(function () {
                const $select = $(this);
                const initialId = $select.data('initial-id');
                const initialText = $select.data('initial-text');
            
                $select.select2({
                    width: '100%',
                    placeholder: '相談を検索',
                    minimumInputLength: 1,
                    allowClear: true,
                    dropdownParent: $('#editModal'), // ← モーダル内なら必須
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する相談は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("consultations.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results })
                    }
                });
            
                // 初期値を選択済み状態に反映
                if (initialId && initialText) {
                    const option = new Option(initialText, initialId, true, true);
                    $select.append(option).trigger('change');
                }
            });
            
             // ▼ 受任案件
            $('.select-business-edit').each(function () {
                const $select = $(this);
                const initialId = $select.data('initial-id');
                const initialText = $select.data('initial-text');
            
                $select.select2({
                    width: '100%',
                    placeholder: '受任案件を検索',
                    minimumInputLength: 1,
                    allowClear: true,
                    dropdownParent: $('#editModal'), // ← モーダル内なら必須
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する受任案件は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("businesses.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results })
                    }
                });
            
                // 初期値を選択済み状態に反映
                if (initialId && initialText) {
                    const option = new Option(initialText, initialId, true, true);
                    $select.append(option).trigger('change');
                }
            });
            
            // ▼ 裁判所
            $('.select-court-edit').each(function () {
                const $select = $(this);
                const initialId = $select.data('initial-id');
                const initialText = $select.data('initial-text');
            
                $select.select2({
                    width: '100%',
                    placeholder: '裁判所を検索',
                    minimumInputLength: 1,
                    allowClear: true,
                    dropdownParent: $('#editModal'), // ← モーダル内なら必須
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する裁判所は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("courts.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results })
                    }
                });
            
                // 初期値を選択済み状態に反映
                if (initialId && initialText) {
                    const option = new Option(initialText, initialId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ 顧問契約
            $('.select-advisory-edit').each(function () {
                const $select = $(this);
                const initialId = $select.data('initial-id');
                const initialText = $select.data('initial-text');
            
                $select.select2({
                    width: '100%',
                    placeholder: '顧問契約を検索',
                    minimumInputLength: 1,
                    allowClear: true,
                    dropdownParent: $('#editModal'), // ← モーダル内なら必須
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する顧問契約は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("advisory.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results })
                    }
                });
            
                // 初期値を選択済み状態に反映
                if (initialId && initialText) {
                    const option = new Option(initialText, initialId, true, true);
                    $select.append(option).trigger('change');
                }
            });

            // ▼ 顧問相談
            $('.select-advisory-consultation-edit').each(function () {
                const $select = $(this);
                const initialId = $select.data('initial-id');
                const initialText = $select.data('initial-text');
            
                $select.select2({
                    width: '100%',
                    placeholder: '顧問相談を検索',
                    minimumInputLength: 1,
                    allowClear: true,
                    dropdownParent: $('#editModal'), // ← モーダル内なら必須
                    language: {
                        inputTooShort: () => '1文字以上入力してください',
                        searching: () => '検索中...',
                        noResults: () => '一致する顧問相談は見つかりません'
                    },
                    ajax: {
                        url: '{{ route("advisory_consultation.search") }}',
                        dataType: 'json',
                        delay: 300,
                        data: params => ({ q: params.term }),
                        processResults: data => ({ results: data.results })
                    }
                });
            
                // 初期値を選択済み状態に反映
                if (initialId && initialText) {
                    const option = new Option(initialText, initialId, true, true);
                    $select.append(option).trigger('change');
                }
            });
        });
    </script>
    <style>
        #sidebar.closed {
            width: 0;
            overflow: hidden;
        }

        /* 高さ・余白などを明示して揃える */
        .select2-container--default .select2-selection--single {
            height: 2.5rem !important;          /* h-10 相当 = 40px */
            padding: 0.5rem 0.75rem !important; /* py-2 px-3 */
            border: 1px solid #d1d5db !important;
            border-radius: 0.25rem !important;
            font-size: 0.875rem;
            background-color: #fff;
            box-sizing: border-box;
        }
        
        /* 内側テキストの高さ調整 */
        .select2-container--default .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
        }
        
        /* ▼アイコン部分の高さ調整 */
        .select2-container--default .select2-selection__arrow {
            height: 2.5rem !important;
        }
    </style>
</head>
<body class="bg-gray-200 flex">
    <div class="w-full min-h-screen bg-gray-100 flex">
        <!-- サイドメニュー（sidebar.blade.php を読み込む） -->
        @include('layouts.sidebar')

        <div class="flex-1">
            <!-- メインコンテンツ -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
    @yield('scripts')
    <script>
        function resetPassword(userId) {
            const btn = document.getElementById('resetSubmitBtn');
            btn.disabled = true;
            btn.textContent = '実行中...';
        
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
            fetch(`/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('newPasswordArea').classList.remove('hidden');
                document.getElementById('newPasswordArea').textContent = data.newPassword;
                btn.style.display = 'none';
            })
            .catch(err => {
                alert('初期化に失敗しました');
                console.error(err);
            });
        }
        
        function closeResetModal() {
            document.getElementById('resetModal').classList.add('hidden');
            // リセット状態に戻す（再度開けるように）
            document.getElementById('resetSubmitBtn').disabled = false;
            document.getElementById('resetSubmitBtn').textContent = '初期化する';
            document.getElementById('newPasswordArea').classList.add('hidden');
            document.getElementById('newPasswordArea').textContent = '';
        }
    </script>   
</body>
</html>