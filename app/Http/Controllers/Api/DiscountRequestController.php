<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\DiscountRequest;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DiscountRequestController extends Controller
{
    // POST /discount-requests  (open endpoint as requested)

    public function index(Request $request)
    {
        // If DataTables is calling this endpoint, it sends "draw"
        $isDataTables = $request->has('draw');

        // -----------------------------
        // Legacy fallback (non-datatables)
        // -----------------------------
        if (!$isDataTables) {
            $rows = DiscountRequest::orderBy('created_at','desc')
                ->take(200)
                ->get()
                ->map(function($r) {
                    $items = $r->items_json;
                    if (is_string($items)) {
                        $items = json_decode($items, true) ?: [];
                    }

                    return [
                        'id' => $r->id,
                        'store_login_id' => $r->store_login_id,
                        'salesman' => $r->salesman ?? null,
                        'store_name' => $r->store_name ?? $r->store_login_id ?? null,
                        'customer_mobile' => $r->customer_mobile ?? null,
                        'items' => $items,
                        'subtotal' => (float) ($r->subtotal ?? 0),
                        'total_vat' => (float) ($r->total_vat ?? 0),
                        'total_payable' => (float) ($r->total_payable ?? 0),
                        'discount_requested' => (float) ($r->discount_requested ?? 0),
                        'status' => $r->status ?? 'pending',
                        'created_at' => $r->created_at,
                    ];
                });

            return response()->json(['ok' => true, 'data' => $rows]);
        }

        // -----------------------------
        // DataTables server-side section
        // -----------------------------
        $tz = 'Asia/Dhaka';

        $baseQuery = DiscountRequest::query();

        // Filters
        if ($request->filled('status')) {
            $baseQuery->where('status', $request->input('status'));
        }

        if ($request->filled('store')) {
            $store = $request->input('store');
            $baseQuery->where(function ($q) use ($store) {
                $q->where('store_name', 'like', "%{$store}%")
                ->orWhere('store_login_id', 'like', "%{$store}%");
            });
        }

        if ($request->filled('customer')) {
            $customer = $request->input('customer');
            $baseQuery->where('customer_mobile', 'like', "%{$customer}%");
        }

        // Date range (created_at) with BD timezone input -> convert to UTC for DB
        if ($request->filled('date_from')) {
            $fromUtc = Carbon::parse($request->input('date_from'), $tz)
                ->startOfDay()
                ->timezone('UTC');
            $baseQuery->where('created_at', '>=', $fromUtc);
        }

        if ($request->filled('date_to')) {
            $toUtc = Carbon::parse($request->input('date_to'), $tz)
                ->endOfDay()
                ->timezone('UTC');
            $baseQuery->where('created_at', '<=', $toUtc);
        }

        // Global search (DataTables)
        $search = $request->input('search.value');
        if ($search) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                ->orWhere('store_login_id', 'like', "%{$search}%")
                ->orWhere('customer_mobile', 'like', "%{$search}%")
                ->orWhere('salesman', 'like', "%{$search}%")
                ->orWhere('temp_cart_id', 'like', "%{$search}%");
            });
        }

        $recordsTotal = DiscountRequest::count();
        $recordsFiltered = (clone $baseQuery)->count();

        // Ordering (map DataTables column index -> DB column)
        $orderColumnIndex = (int) ($request->input('order.0.column', 1)); // default Date column
        $orderDir = $request->input('order.0.dir', 'desc') === 'asc' ? 'asc' : 'desc';

        // DataTables columns: 0=SR, 1=Date, 2=Store, 3=Customer, ...
        $orderMap = [
            1 => 'created_at',
            2 => 'store_name',
            3 => 'customer_mobile',
            7 => 'status',
        ];

        $orderBy = $orderMap[$orderColumnIndex] ?? 'created_at';
        $baseQuery->orderBy($orderBy, $orderDir);

        // Paging
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 25);

        $rows = $baseQuery->skip($start)->take($length)->get();

        $data = $rows->values()->map(function ($r, $i) use ($start, $tz) {

            // items decode (for total items count)
            $items = $r->items_json;
            if (is_string($items)) {
                $items = json_decode($items, true) ?: [];
            }
            if (!is_array($items)) $items = [];

            $totalQty = 0;
            foreach ($items as $it) {
                $totalQty += (int) ($it['quantity'] ?? $it['qty'] ?? 0);
            }

            $status = $r->status ?? 'pending';
            $badgeClass = $status === 'approved' ? 'success' : ($status === 'rejected' ? 'danger' : 'warning');

            $createdBD = $r->created_at
                ? Carbon::parse($r->created_at)->timezone($tz)->format('d M Y, h:i A')
                : '-';

            // action buttons html
            $buttons = '
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-sm btn-info view-btn" data-id="'.$r->id.'">
                        <i class="ri-eye-fill"></i> View
                    </button>';

            if ($status === 'pending') {
                $buttons .= '
                    <button class="btn btn-sm btn-success approve-btn" data-id="'.$r->id.'">
                        <i class="ri-check-line"></i> Approve
                    </button>
                    <button class="btn btn-sm btn-danger reject-btn" data-id="'.$r->id.'">
                        <i class="ri-close-line"></i> Reject
                    </button>';
            } else {
                $buttons .= '
                    <span class="badge bg-'.$badgeClass.' status-badge">'.$status.'</span>';
            }

            $buttons .= '
                    <button class="btn btn-sm btn-outline-danger delete-btn" data-id="'.$r->id.'">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                </div>';

            return [
                'id' => $r->id,
                'sr' => str_pad((string)($start + $i + 1), 2, '0', STR_PAD_LEFT),
                'created_at' => $createdBD,
                'store_name' => $r->store_name ?? $r->store_login_id ?? '-',
                'customer_mobile' => $r->customer_mobile ?? '-',
                'total_items' => $totalQty,
                'total_payable_fmt' => '৳ ' . number_format((float)($r->total_payable ?? 0), 2),
                'discount_requested_fmt' => '৳ ' . number_format((float)($r->discount_requested ?? 0), 2),
                'status_badge' => '<span class="badge bg-'.$badgeClass.' status-badge">'.$status.'</span>',
                'action' => $buttons,
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw', 1),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function destroy($id)
    {
        $dr = DiscountRequest::find($id);
        if (!$dr) return response()->json(['ok' => false, 'message' => 'not found'], 404);

        // If pending, it's safer to notify POS as rejected before deleting
        if (($dr->status ?? 'pending') === 'pending' && $dr->pos_callback_url) {
            try {
                $callbackPayload = [
                    'temp_cart_id' => $dr->temp_cart_id,
                    'status' => 'rejected',
                    'discount' => 0,
                    'request_id' => $dr->id,
                    'items' => is_string($dr->items_json) ? (json_decode($dr->items_json, true) ?: []) : ($dr->items_json ?? []),
                ];
                Http::timeout(5)->post($dr->pos_callback_url, $callbackPayload);
            } catch (\Throwable $ex) {
                Log::error('Delete callback failed', ['err' => $ex->getMessage()]);
            }
        }

        $dr->delete();

        return response()->json(['ok' => true]);
    }

    // GET /api/discount-requests/{id}
    public function show($id)
    {
        $r = DiscountRequest::find($id);
        if (!$r) return response()->json(['ok' => false, 'message' => 'not found'], 404);

        // expose items as array if stored as JSON string
        $items = $r->items_json;
        if (is_string($items)) {
            $parsed = json_decode($items, true);
            $items = $parsed === null ? [] : $parsed;
        }

        $payload = [
            'id' => $r->id,
            'store_login_id' => $r->store_login_id,
            'salesman' => $r->salesman ?? null,
            'store_name' => $r->store_name ?? null,
            'customer_mobile' => $r->customer_mobile,
            'items_json' => $items,
            'subtotal' => (float)$r->subtotal,
            'total_vat' => (float)$r->total_vat,
            'total_payable' => (float)$r->total_payable,
            'discount_requested' => (float)$r->discount_requested,
            'pos_callback_url' => $r->pos_callback_url,
            'status' => $r->status,
            'created_at' => $r->created_at,
        ];

        return response()->json(['ok' => true, 'data' => $payload]);
    }

    // PATCH /api/discount-requests/{id}/approve
    public function approve(Request $request, $id)
    {
        return $this->decisionInternal($id, 'approved', $request->all());
    }

    // PATCH /api/discount-requests/{id}/reject
    public function reject(Request $request, $id)
    {
        return $this->decisionInternal($id, 'rejected', $request->all());
    }

    // internal common decision logic
    protected function decisionInternal($id, $status, array $extra = [])
    {
        $dr = DiscountRequest::find($id);
        if (!$dr) return response()->json(['ok'=>false,'message'=>'not found'],404);

        $dr->status = $status;
        $dr->admin_id = auth()->id() ?? null;
        $dr->save();
 
        // Prepare callback payload for POS
        $items = $dr->items_json;
        if (is_string($items)) {
            $items = json_decode($items, true) ?: [];
        }

        $callbackPayload = [
            'temp_cart_id' => $dr->temp_cart_id,
            'status' => $dr->status,
            'discount' => ($dr->status === 'approved') ? floatval($dr->discount_requested) : 0,
            'request_id' => $dr->id,
            'items' => $items,
        ];

        if ($dr->pos_callback_url) {
            try {
                $res = Http::timeout(5)->post($dr->pos_callback_url, $callbackPayload);
                Log::info('Discount callback to POS', ['url' => $dr->pos_callback_url, 'status' => $res->status()]);
            } catch (\Throwable $ex) {
                Log::error('Discount callback failed', ['err' => $ex->getMessage(), 'url' => $dr->pos_callback_url]);
            }
        }

        return response()->json(['ok'=>true]);
    }

    public function store(Request $r)
    {
        $data = $r->only([
            'temp_cart_id','store_login_id','salesman','customer_mobile','sales_type',
            'items','subtotal','total_vat','total_payable','discount_requested','total_after_discount','pos_callback_url','shop_name',
        ]);

        $tempCartId = $data['temp_cart_id'] ?? null;

        // Defensive: require temp_cart_id
        if (!$tempCartId) {
            return response()->json(['ok' => false, 'message' => 'missing temp_cart_id'], 422);
        }

        // Try to find an existing request for the same cart which is still actionable
        $existing = DiscountRequest::where('temp_cart_id', $tempCartId)
            ->whereIn('status', ['pending', 'draft'])   // adjust allowed statuses if needed
            ->latest()
            ->first();

        if ($existing) {
            // update fields that should be overwritten when re-requesting
            $existing->store_name         = $data['shop_name'] ?? $existing->store_name;
            $existing->salesman           = $data['salesman'] ?? $existing->salesman;
            $existing->customer_mobile    = $data['customer_mobile'] ?? $existing->customer_mobile;
            $existing->sales_type         = $data['sales_type'] ?? $existing->sales_type;
            $existing->items_json         = $data['items'] ?? $existing->items_json;
            $existing->subtotal           = $data['subtotal'] ?? $existing->subtotal;
            $existing->total_vat          = $data['total_vat'] ?? $existing->total_vat;
            $existing->total_payable      = $data['total_payable'] ?? $existing->total_payable;
            $existing->discount_requested = $data['discount_requested'] ?? $existing->discount_requested;
            $existing->total_after_discount = $data['total_after_discount'] ?? $existing->total_after_discount;
            $existing->pos_callback_url   = $data['pos_callback_url'] ?? $existing->pos_callback_url;
            $existing->status             = 'pending';
            $existing->save();

            return response()->json(['ok' => true, 'id' => $existing->id, 'updated' => true]);
        }

        // No existing pending/draft - create new row
        $dr = DiscountRequest::create([
            'temp_cart_id' => $tempCartId,
            'store_name' => $data['shop_name'] ?? null,
            'salesman' => $data['salesman'] ?? null,
            'customer_mobile' => $data['customer_mobile'] ?? null,
            'sales_type' => $data['sales_type'] ?? null,
            'items_json' => $data['items'] ?? [],
            'subtotal' => $data['subtotal'] ?? 0,
            'total_vat' => $data['total_vat'] ?? 0,
            'total_payable' => $data['total_payable'] ?? 0,
            'discount_requested' => $data['discount_requested'] ?? 0,
            'total_after_discount' => $data['total_after_discount'] ?? 0,
            'pos_callback_url' => $data['pos_callback_url'] ?? null,
            'status' => 'pending'
        ]);

        return response()->json(['ok' => true, 'id' => $dr->id, 'updated' => false]);
    }


    // status by temp cart (POS poll fallback)
    public function statusByTempCart($tempCartId)
    {
        $dr = DiscountRequest::where('temp_cart_id', $tempCartId)->latest()->first();
        if (!$dr) return response()->json(['ok'=>false,'status'=>null]);
        return response()->json(['ok'=>true,'status'=>$dr->status,'id'=>$dr->id]);
    }

    // Admin decision endpoint (POST). Example route: POST /discount-requests/decision
    // expects: id, status ('approved'|'rejected'), optional items with per-item total_discount
    public function decision(Request $r)
    {
        $dr = DiscountRequest::find($r->input('id'));
        if (!$dr) return response()->json(['ok'=>false,'message'=>'not found'],404);

        $status = $r->input('status');
        if (!in_array($status, ['approved','rejected'])) {
            return response()->json(['ok'=>false,'message'=>'invalid status'], 422);
        }

        // optionally admin adjustments (per-item discounts) can be sent in payload
        $providedItems = $r->input('items'); // optional

        $dr->status = $status;
        $dr->admin_id = auth()->id() ?? null;
        // if admin updated items or discount, save them
        if ($providedItems) {
            $dr->items_json = $providedItems;
        }
        if ($r->has('discount_requested')) {
            $dr->discount_requested = $r->input('discount_requested');
        }
        $dr->save();

        $callbackPayload = [
            'temp_cart_id' => $dr->temp_cart_id,
            'status' => $dr->status,
            'discount' => ($dr->status === 'approved') ? floatval($dr->discount_requested) : 0,
            'request_id' => $dr->id,
            'items' => $dr->items_json
        ];

        // send HMAC signature header if shared secret is configured
        $posUrl = $dr->pos_callback_url;
        if ($posUrl) {
            try {
                $secret = env('MOTHER_POS_SHARED_SECRET', null);
                $jsonBody = json_encode($callbackPayload);
                $signature = $secret ? hash_hmac('sha256', $jsonBody, $secret) : null;

                $client = Http::timeout(5);
                if ($signature) {
                    $client = $client->withHeaders(['X-SIGNATURE' => $signature]);
                }
                $res = $client->post($posUrl, $callbackPayload);
                Log::info('Discount callback to POS', ['url'=>$posUrl,'status'=>$res->status(),'body'=>$callbackPayload]);
            } catch (\Throwable $ex) {
                Log::error('Discount callback failed', ['err'=>$ex->getMessage(),'url'=>$posUrl,'body'=>$callbackPayload]);
            }
        }

        return response()->json(['ok'=>true]);
    }
}
