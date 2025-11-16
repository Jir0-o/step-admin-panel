<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $stores = Store::orderBy('id')->get();
                return response()->json(['ok' => true, 'data' => $stores], 200);
            }
            return view('backend.admin.store-create');
        } catch (\Throwable $e) {
            Log::error('Store index error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to load stores'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'base_url' => 'nullable|url|max:255',
                'user_email' => 'nullable|email|max:255',
                'user_password' => 'nullable|string|min:4|max:255',
            ]);

            if (!empty($data['user_password'])) {
                $data['user_password'] = Hash::make($data['user_password']);
            } else {
                $data['user_password'] = null;
            }

            $store = Store::create($data);

            return response()->json(['ok' => true, 'data' => $store, 'message' => 'Store created'], 201);
        } catch (ValidationException $ve) {
            return response()->json(['ok' => false, 'message' => 'Validation failed', 'errors' => $ve->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Store create error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to create store'], 500);
        }
    }

    public function show(Store $store)
    {
        try {
            return response()->json(['ok' => true, 'data' => $store], 200);
        } catch (\Throwable $e) {
            \Log::error('Store show error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to load store'], 500);
        }
    }

    public function update(Request $request, Store $store)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'base_url' => 'nullable|url|max:255',
                'user_email' => 'nullable|email|max:255',
                'user_password' => 'nullable|string|min:4|max:255',
            ]);

            if (!empty($data['user_password'])) {
                $data['user_password'] = Hash::make($data['user_password']);
            } else {
                unset($data['user_password']);
            }

            $store->update($data);

            return response()->json(['ok' => true, 'data' => $store, 'message' => 'Store updated'], 200);
        } catch (ValidationException $ve) {
            return response()->json(['ok' => false, 'message' => 'Validation failed', 'errors' => $ve->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('Store update error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to update store'], 500);
        }
    }

    public function destroy(Store $store)
    {
        try {
            $store->delete();
            return response()->json(['ok' => true, 'message' => 'Store deleted'], 200);
        } catch (\Throwable $e) {
            Log::error('Store delete error: '.$e->getMessage());
            return response()->json(['ok' => false, 'message' => 'Failed to delete store'], 500);
        }
    }
}
