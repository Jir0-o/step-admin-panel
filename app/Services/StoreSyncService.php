<?php

namespace App\Services;

use App\Models\Store;
use App\Services\StoreApiClient;
use Illuminate\Support\Facades\Log;

class StoreSyncService
{
    protected StoreApiClient $client;

    public function __construct(StoreApiClient $client = null)
    {
        $this->client = $client ?? new StoreApiClient();
    }

    /**
     * Sync all stores immediately. Returns map of results by store id.
     */
    public function syncAll(array $filters = []): array
    {
        $q = Store::query();
        if (!empty($filters['store_ids'])) {
            $q->whereIn('id', $filters['store_ids']);
        }
        if (!empty($filters['active'])) {
            $q->where('is_active', 1);
        }

        $results = [];
        foreach ($q->get() as $store) {
            try {
                $login = $this->client->login($store);
                if (!$login['ok']) {
                    $results[$store->id] = ['ok' => false, 'message' => $login['message'] ?? 'login_failed'];
                    continue;
                }

                $res = $this->client->fetchExport($store, $login, $filters['params'] ?? []);
                $results[$store->id] = $res;

                // Optionally: persist `$res['data']` into your central DB table
                // e.g. ExportedData::create(['store_id'=>$store->id,'payload'=>json_encode($res['data'])])
            } catch (\Throwable $e) {
                Log::error("Store sync error (id {$store->id}): ".$e->getMessage());
                $results[$store->id] = ['ok' => false, 'message' => $e->getMessage()];
            }
        }

        return $results;
    }
}
