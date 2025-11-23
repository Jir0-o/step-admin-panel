<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DiscountRequest;
use Illuminate\Validation\ValidationException;

class DiscountRequestController extends Controller
{
    // POST /api/discount-requests
    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id' => 'required|integer',
            'store_name' => 'nullable|string',
            'temp_cart_id' => 'nullable|integer',
            'requested_by' => 'nullable|integer',
            'requested_by_name' => 'nullable|string',
            'items' => 'required|array|min:1',
            'requested_amount' => 'required|numeric',
            'total_amount' => 'required|numeric'
        ]);

        $req = DiscountRequest::create([
            'store_id' => $data['store_id'],
            'store_name' => $data['store_name'] ?? null,
            'temp_cart_id' => $data['temp_cart_id'] ?? null,
            'requested_by' => $data['requested_by'] ?? null,
            'requested_by_name' => $data['requested_by_name'] ?? null,
            'items' => $data['items'],
            'requested_amount' => $data['requested_amount'],
            'total_amount' => $data['total_amount'],
            'status' => 'pending'
        ]);

        return response()->json(['ok' => true, 'data' => $req], 201);
    }

    // GET /api/discount-requests -> list (admin)
    public function index(Request $request)
    {
        $q = DiscountRequest::query()->orderBy('created_at','desc');
        $rows = $q->get();
        return response()->json(['ok'=>true,'data'=>$rows]);
    }

    // GET /api/discount-requests/{id}
    public function show(DiscountRequest $discountRequest)
    {
        return response()->json(['ok'=>true,'data'=>$discountRequest]);
    }

    // GET /api/discount-requests/status/{tempCartId}
    // POS polls this endpoint to know status for a given temp_cart_id
    public function statusByTempCart($tempCartId)
    {
        $req = DiscountRequest::where('temp_cart_id', $tempCartId)
                ->orderBy('created_at','desc')
                ->first();

        if (! $req) {
            return response()->json(['ok'=>false,'message'=>'not-found'], 404);
        }
        return response()->json(['ok'=>true,'data'=>['id'=>$req->id,'status'=>$req->status]]);
    }

    // PATCH /api/discount-requests/{id}/approve
    public function approve(Request $request, DiscountRequest $discountRequest)
    {
        // optionally validate role/auth here (only admin)
        $discountRequest->status = 'approved';
        $discountRequest->approved_by = $request->user()->id ?? null;
        $discountRequest->approved_by_name = $request->user()->name ?? null;
        $discountRequest->approved_at = now();
        $discountRequest->save();

        return response()->json(['ok'=>true,'data'=>$discountRequest]);
    }

    // PATCH /api/discount-requests/{id}/reject
    public function reject(Request $request, DiscountRequest $discountRequest)
    {
        $discountRequest->status = 'rejected';
        $discountRequest->approved_by = $request->user()->id ?? null;
        $discountRequest->approved_by_name = $request->user()->name ?? null;
        $discountRequest->approved_at = now();
        $discountRequest->save();

        return response()->json(['ok'=>true,'data'=>$discountRequest]);
    }
}
