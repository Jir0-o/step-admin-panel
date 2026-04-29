<?php

namespace App\Http\Controllers;

use App\Services\StoreOverviewService;
use App\Services\StoreTokenSyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private readonly StoreOverviewService $storeOverviewService,
        private readonly StoreTokenSyncService $storeTokenSyncService
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

        if ($forceRefresh) {
            $this->storeTokenSyncService->syncForUser($userId, null, null, true);
        }

        $payload = $this->storeOverviewService->getOverviewForUser($userId, $forceRefresh);

        return response()->json([
            'ok' => true,
            'data' => $payload,
        ]);
    }

    public function create()
    {
    }

    public function store(\Illuminate\Http\Request $request)
    {
    }

    public function show(string $id)
    {
    }

    public function edit(string $id)
    {
    }

    public function update(\Illuminate\Http\Request $request, string $id)
    {
    }

    public function destroy(string $id)
    {
    }
}
