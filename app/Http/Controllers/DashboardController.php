<?php

namespace App\Http\Controllers;

use App\Services\StoreOverviewService;
use App\Services\StoreTokenSyncService;
use App\Services\StoreTokenService;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly StoreOverviewService $storeOverviewService,
        private readonly StoreTokenSyncService $storeTokenSyncService,
        private readonly StoreTokenService $storeTokenService
    ) {
    }

    public function index()
    {
        $user = Auth::user();

        return view('dashboard', compact('user'));
    }

    public function overview(): JsonResponse
    {
        $userId = (int) Auth::id();
        $forceRefresh = request()->boolean('refresh');
        $quick = request()->boolean('quick');

        $payload = $this->storeOverviewService->getOverviewForUser(
            $userId,
            $forceRefresh,
            $quick
        );

        return response()->json([
            'ok' => true,
            'data' => $payload,
        ]);
    }

    public function overviewStore(Store $store): JsonResponse
    {
        $row = $this->storeOverviewService->getStoreOverviewForUser(
            $store,
            (int) Auth::id(),
            request()->boolean('refresh')
        );

        return response()->json([
            'ok' => (bool) ($row['ok'] ?? false),
            'message' => $row['message'] ?? null,
            'data' => $row,
        ]);
    }

    public function tokenStatus(): JsonResponse
    {
        $userId = (int) Auth::id();
        $forceRefresh = request()->boolean('refresh');

        return response()->json([
            'ok' => true,
            'data' => $this->storeTokenService->tokenStatusForUser($userId, $forceRefresh),
        ]);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show(string $id)
    {
    }

    public function edit(string $id)
    {
    }

    public function update(Request $request, string $id)
    {
    }

    public function destroy(string $id)
    {
    }
}
