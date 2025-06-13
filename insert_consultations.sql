USE laravel;
SET NAMES utf8mb4;

INSERT INTO consultations (
    client_id, consultation_party, title, status, status_detail,
    case_summary, special_notes, inquirycontent,
    firstchoice_datetime, secondchoice_datetime,
    inquirytype, consultationtype, case_category, case_subcategory,
    opponent_confliction, consultation_receptiondate, consultation_firstdate,
    enddate, consultation_notreason, consultation_feedback,
    reason_termination, reason_termination_detail, office_id,
    created_at, updated_at
) VALUES
-- ① 交通事故相談（ステータス：受付中）
(1, 1, '交通事故の相談', 1, '初回ヒアリング済',
'信号無視による事故の相手方からの請求について相談',
'保険会社との交渉状況に留意が必要',
'電話フォーム経由での相談申込',
'2025-06-15 10:00:00', '2025-06-15 15:00:00',
1, 1, 1, 1, 1,
'2025-06-14', '2025-06-15', NULL, NULL, NULL,
NULL, NULL, 1, NOW(), NOW()),

-- ② 相続相談（ステータス：契約不成立）
(2, 2, '相続に関するトラブル相談', 4, '費用の折り合いがつかず終了',
'被相続人の不動産の名義変更に関する家族内紛争',
'母親との連絡が取れない状況が継続中',
'来所での相談希望',
'2025-06-10 11:00:00', NULL,
3, 1, 3, 8, 1,
'2025-06-08', '2025-06-10', '2025-06-11', 2, 2,
'費用面の相違', '分割払い不可による終了', 2, NOW(), NOW()),

-- ③ 労働問題相談（ステータス：受任済）
(3, 1, '解雇に関する相談', 6, '受任完了・案件化済',
'退職勧奨の圧力を受けた件について不当解雇の可能性あり',
'労働審判を視野に入れた対応検討中',
'電話（直受）による受付',
'2025-06-12 09:30:00', '2025-06-12 13:00:00',
1, 2, 2, 3, 1,
'2025-06-11', '2025-06-12', NULL, NULL, 1,
'受任し案件登録済', NULL, 1, NOW(), NOW());
