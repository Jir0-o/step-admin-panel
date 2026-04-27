<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class StoreOverviewService
{
    public const TABLES = 'products,suppliers,cart_informtion,expense_details,banner_information,salesman_target_summary';


    public function __construct(private readonly StoreTokenService $tokenService)
    {
    }

    public function getOverviewForUser(int $userId, bool $forceRefresh = false): array
    {
        $stores = Store::query()->orderBy('id')->get(['id', 'name', 'base_url', 'login_api_url']);

        $storeRows = [];
        $totals = [
            'store_count' => $stores->count(),
            'products' => 0,
            'suppliers' => 0,
            'sales_year' => 0,
            'sales_month' => 0,
            'sales_today' => 0,
            'sales_yesterday' => 0,
            'discount_today' => 0,
            'discount_month' => 0,
            'profit' => 0,
            'expenses' => 0,
            'income_today' => 0,
            'income_yesterday' => 0,

            'target_monthly' => 0,
            'achievement_monthly' => 0,
            'percentage_monthly' => 0,

            'failed_stores' => 0,
        ];

        foreach ($stores as $store) {
            $cacheKey = "store-overview:user:{$userId}:store:{$store->id}";

            if ($forceRefresh) {
                Cache::forget($cacheKey);
            }

            $row = Cache::get($cacheKey);
            if (! is_array($row)) {
                $row = $this->fetchStoreSummaryRow($store, $userId);

                $ttl = ($row['ok'] ?? false)
                    ? now()->addMinutes(5)
                    : now()->addSeconds(45);

                Cache::put($cacheKey, $row, $ttl);
            }

            $storeRows[] = $row;

            if (! ($row['ok'] ?? false)) {
                $totals['failed_stores']++;
                continue;
            }

            $totals['products'] += (int) ($row['products_total'] ?? 0);
            $totals['suppliers'] += (int) ($row['suppliers_total'] ?? 0);
            $totals['sales_year'] += (float) ($row['sales_year'] ?? 0);
            $totals['sales_today'] += (float) ($row['sales_today'] ?? 0);
            $totals['sales_yesterday'] += (float) ($row['sales_yesterday'] ?? 0);
            $totals['profit'] += (float) ($row['profit'] ?? 0);
            $totals['discount_today'] += (float) ($row['discount_today'] ?? 0);
            $totals['discount_month'] += (float) ($row['discount_month'] ?? 0);
            $totals['expenses'] += (float) ($row['expenses'] ?? 0);
            $totals['income_today'] += (float) ($row['income_today'] ?? 0);
            $totals['income_yesterday'] += (float) ($row['income_yesterday'] ?? 0);
            $totals['sales_month'] += (float) ($row['sales_month'] ?? 0);
            $totals['target_monthly'] += (float) ($row['store_target_monthly'] ?? 0);
            $totals['achievement_monthly'] += (float) ($row['monthly_achievement'] ?? 0);
        }

        $totals['percentage_monthly'] = $totals['target_monthly'] > 0
            ? round(($totals['achievement_monthly'] / $totals['target_monthly']) * 100, 2)
            : 0;

        $chart = collect($storeRows)
            ->filter(fn (array $row) => $row['ok'] ?? false)
            ->sortByDesc('sales_year')
            ->take(10)
            ->values()
            ->map(fn (array $row) => [
                'label' => $row['name'],
                'value' => (float) $row['sales_year'],
            ])
            ->all();

        return [
            'generated_at' => now()->toDateTimeString(),
            'totals' => $totals,
            'stores' => $storeRows,
            'chart' => $chart,
        ];
    }

    public function fetchStoreSummaryRow(Store $store, int $userId): array
    {
        try {
            $result = $this->requestStoreSummary($store, $userId, ['tables' => self::TABLES]);

            if (! ($result['ok'] ?? false)) {
                return [
                    'id' => $store->id,
                    'name' => $store->name,
                    'ok' => false,
                    'message' => $result['message'] ?? 'Failed to fetch summary',
                ];
            }

            $payload = $result['payload'] ?? [];
            $results = $payload['results'] ?? $payload['data'] ?? $payload;

            $cart = Arr::get($results, 'cart_informtion', []);
            $expenses = Arr::get($results, 'expense_details', []);
            $products = Arr::get($results, 'products', []);
            $suppliers = Arr::get($results, 'suppliers', []);
            $targetSummary = Arr::get($results, 'salesman_target_summary', []);

            return [
                'id' => $store->id,
                'name' => $store->name,
                'ok' => true,

                'sales_today' => (float) Arr::get($targetSummary, 'today_sales', Arr::get($cart, 'today_total_amount', Arr::get($cart, 'total_amount_today', 0))),
                'sales_month' => (float) Arr::get($targetSummary, 'this_month_sales', Arr::get($cart, 'total_amount_month', 0)),
                'sales_year' => (float) Arr::get($targetSummary, 'this_year_sales', Arr::get($cart, 'total_amount_year', 0)),
                'sales_yesterday' => (float) Arr::get($cart, 'yesterday_total_amount', 0),
                'discount_today' => (float) Arr::get($cart, 'total_discount_today', 0),
                'discount_month' => (float) Arr::get($cart, 'total_discount_month', 0),

                'profit' => (float) Arr::get($cart, 'total_profit', 0),
                'income_today' => (float) Arr::get($cart, 'today_total_profit', Arr::get($cart, 'profit_today', 0)),
                'income_yesterday' => (float) Arr::get($cart, 'yesterday_total_profit', 0),
                'expenses' => (float) Arr::get($expenses, 'total_amount', 0),

                'products_total' => (int) Arr::get($products, 'total_count', 0),
                'suppliers_total' => (int) Arr::get($suppliers, 'total_count', 0),

                'salesmen_count' => (int) Arr::get($targetSummary, 'salesmen_count', 0),

                'store_target_monthly' => (float) Arr::get($targetSummary, 'store_target_monthly', 0),
                'monthly_achievement' => (float) Arr::get($targetSummary, 'monthly_achievement', 0),
                'monthly_percentage' => (float) Arr::get($targetSummary, 'monthly_percentage', 0),

                'store_target_yearly' => (float) Arr::get($targetSummary, 'store_target_yearly', 0),
                'yearly_achievement' => (float) Arr::get($targetSummary, 'yearly_achievement', 0),
                'yearly_percentage' => (float) Arr::get($targetSummary, 'yearly_percentage', 0),

                'raw' => [
                    'cart_informtion' => $cart,
                    'expense_details' => $expenses,
                    'products' => $products,
                    'suppliers' => $suppliers,
                    'banner_information' => Arr::get($results, 'banner_information', []),
                    'salesman_target_summary' => $targetSummary,
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    private function requestStoreSummary(Store $store, int $userId, array $params): array
    {
        $url = $this->tokenService->buildStoreUrl($store, '/api/manager/data/summary');

        $postResponse = $this->tokenService->sendAuthorized($store, $userId, 'POST', $url, [
            'timeout' => 20,
            'json' => $params,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        if ($postResponse->successful()) {
            return $this->normalizeSummaryResponse($postResponse->json(), $store->id, $store->name);
        }

        $fallbackStatuses = [404, 405, 415, 422];
        if (! in_array($postResponse->status(), $fallbackStatuses, true)) {
            return [
                'ok' => false,
                'message' => 'Remote summary returned status '.$postResponse->status(),
            ];
        }

        $getResponse = $this->tokenService->sendAuthorized($store, $userId, 'GET', $url, [
            'timeout' => 20,
            'query' => $params,
        ]);

        if (! $getResponse->successful()) {
            return [
                'ok' => false,
                'message' => 'Remote summary returned status '.$getResponse->status(),
            ];
        }

        return $this->normalizeSummaryResponse($getResponse->json(), $store->id, $store->name);
    }

    private function normalizeSummaryResponse(array $payload, int $storeId, string $storeName): array
    {
        if (($payload['ok'] ?? true) === false) {
            return [
                'ok' => false,
                'message' => $payload['message'] ?? 'Remote server returned an error',
                'store_id' => $storeId,
                'store_name' => $storeName,
            ];
        }

        return [
            'ok' => true,
            'payload' => $payload,
            'store_id' => $storeId,
            'store_name' => $storeName,
        ];
    }
}
