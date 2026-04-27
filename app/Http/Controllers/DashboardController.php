<?php

namespace App\Http\Controllers;

use App\Services\StoreOverviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(private readonly StoreOverviewService $storeOverviewService)
    {
    }

    public function index()
    {
        $user = Auth::user();

        return view('dashboard', compact('user'));
    }

    public function overview(): JsonResponse
    {
        $payload = $this->storeOverviewService->getOverviewForUser((int) Auth::id(), request()->boolean('refresh'));

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
