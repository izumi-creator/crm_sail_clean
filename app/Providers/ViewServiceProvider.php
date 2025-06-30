<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        // 各連動セレクトの変数名・対象ビュー・変換ロジックを定義
        $viewVariableMap = [
            'routedetailOptions' => [
                'views' => ['inquiry.', 'consultation.', 'business.', 'advisory.'],
                'source' => function () {
                    $all = config('master.routedetails');
                    $map = config('master.routedetail_map');
                    return self::buildOptionMap($all, $map);
                },
            ],
            'casedetailOptions' => [
                'views' => ['consultation.', 'business.'],
                'source' => function () {
                    $all = config('master.case_subcategories');
                    $map = config('master.case_map');
                    return self::buildOptionMap($all, $map);
                },
            ],

            'relatedtypeOptions' => [
                'views' => ['relatedparty.'],
                'source' => function () {
                    $all = config('master.relatedparties_types');
                    $map = config('master.relatedparties_type_map');
                    return self::buildOptionMap($all, $map);
                },
            ],

            'record2Options' => [
                'views' => ['task.', 'negotiation.'],
                'source' => function () {
                    $all = config('master.records_2');
                    $map = config('master.record2_map');
                    return self::buildOptionMap($all, $map);
                },
            ],

            // ★ ここに今後の動的セレクトを追加すればOK
            // 'courtbranchOptions' => [...]
        ];

        // 対象ビューに必要な変数のみ渡す
        View::composer('*', function ($view) use ($viewVariableMap) {
            $viewName = $view->getName();

            foreach ($viewVariableMap as $varName => $data) {
                foreach ($data['views'] as $prefix) {
                    if (Str::startsWith($viewName, $prefix)) {
                        $view->with($varName, $data['source']());
                        break;
                    }
                }
            }
        });
    }

    /**
     * 親→子のマップを画面用に整形
     */
    private static function buildOptionMap(array $labelSource, array $idMap): array
    {
        $result = [];

        foreach ($idMap as $key => $ids) {
            $result[$key] = collect($ids->all())->map(function ($id) use ($labelSource) {
                return [
                    'id' => $id,
                    'label' => $labelSource[$id] ?? '（未定義）',
                ];
            })->toArray();
        }

        return $result;
    }
}