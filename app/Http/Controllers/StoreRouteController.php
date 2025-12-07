<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreRoute;
use Illuminate\Http\Request;

class StoreRouteController extends Controller
{
    // Show create form + list
    public function index()
    {
        // Change 'name' if your column is different (e.g. 'store_name')
        $stores = Store::orderBy('name')->get();
        $routes = StoreRoute::with('store')->orderBy('id', 'desc')->get();

        return view('backend.admin.route', compact('stores', 'routes'));
    }

    // Store new route
    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id'  => 'required|exists:stores,id',
            'base_url'  => 'required|string|max:255',
            'endpoint'  => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $route = StoreRoute::create($data);

        if ($request->ajax()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Route created successfully.',
                'route'   => $route->load('store'),
            ]);
        }

        return back()->with('success', 'Route created successfully.');
    }

    // You are not using this now (modal uses data-* attributes), but keeping it in case
    public function edit(StoreRoute $storeRoute)
    {
        $stores = Store::orderBy('name')->get();

        return view('routes.edit', [
            'route'  => $storeRoute,
            'stores' => $stores,
        ]);
    }

    // Update route (used by AJAX in modal)
    public function update(Request $request, StoreRoute $storeRoute)
    {
        $data = $request->validate([
            'store_id'  => 'required|exists:stores,id',
            'base_url'  => 'required|string|max:255',
            'endpoint'  => 'required|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $storeRoute->update($data);

        if ($request->ajax()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Route updated successfully.',
                'route'   => $storeRoute->fresh()->load('store'),
            ]);
        }

        return redirect()
            ->route('store-routes.index')
            ->with('success', 'Route updated successfully.');
    }

    // Delete route (used by AJAX)
    public function destroy(Request $request, StoreRoute $storeRoute)
    {
        $storeRoute->delete();

        if ($request->ajax()) {
            return response()->json([
                'ok'      => true,
                'message' => 'Route deleted successfully.',
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Route deleted successfully.');
    }
}
