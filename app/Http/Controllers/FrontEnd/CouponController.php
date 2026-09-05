<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Coupon;
use App\CouponUsage;
use Sentinel;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    private function getOrgId()
    {
        $user = Sentinel::check();
        $reg = registeruser($user->id);
        return $reg->organization ?? null;
    }

    // -------------------- LIST --------------------
    public function index(Request $request)
    {
        $orgId = $this->getOrgId();

        $coupons = Coupon::where('organization_id', $orgId)
            ->orderBy('id', 'DESC')
            ->paginate(20);

        return view('FrontEnd.coupons.index', compact('coupons'));
    }

    // -------------------- CREATE FORM --------------------
    public function create()
    {
        return view('FrontEnd.coupons.create');
    }

    // -------------------- STORE (Single or Bulk) --------------------
 public function store(Request $request)
{
    $user = Sentinel::check();
    $orgId = $this->getOrgId();

    try {

        $request->validate([
            'mode' => 'required|in:single,bulk',

            'code' => 'nullable|string|max:50',
            'bulk_count' => 'nullable|integer|min:1|max:5000',

            'name' => 'nullable|string|max:100',
            'type' => 'required|in:flat,percent',
            'value' => 'required|numeric|min:0',

            'min_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',

            'usage_type' => 'required|in:single,multiple',
            'usage_limit' => 'nullable|integer|min:1|max:999999',

            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',

            'status' => 'required|integer|in:0,1',
        ]);

        // ✅ BULK MODE
        if ($request->mode === 'bulk') {

            $count = (int)($request->bulk_count ?? 0);
            if ($count <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Bulk count required.'
                ]);
            }

            if ($request->usage_type === 'multiple' && (!$request->usage_limit || $request->usage_limit <= 0)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usage limit required for multiple use coupon.'
                ]);
            }

            $created = 0;

            for ($i = 0; $i < $count; $i++) {

                do {
                    $code = strtoupper('CPN-' . \Illuminate\Support\Str::random(8));
                    $exists = Coupon::where('organization_id', $orgId)->where('code', $code)->exists();
                } while ($exists);

                Coupon::create([
                    'organization_id' => $orgId,
                    'code' => $code,
                    'name' => $request->name,
                    'type' => $request->type,
                    'value' => $request->value,
                    'min_amount' => $request->min_amount,
                    'max_discount' => $request->max_discount,
                    'usage_type' => $request->usage_type,
                    'usage_limit' => $request->usage_type === 'multiple' ? $request->usage_limit : 1,
                    'used_count' => 0,
                    'start_at' => $request->start_at,
                    'end_at' => $request->end_at,
                    'status' => $request->status,
                    'created_by' => $user->id ?? null,
                ]);

                $created++;
            }

            return response()->json([
                'status' => true,
                'message' => "{$created} coupons created!"
            ]);
        }

        // ✅ SINGLE MODE
        $code = strtoupper(trim($request->code ?? ''));

        if (!$code) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon code required'
            ]);
        }

        $exists = Coupon::where('organization_id', $orgId)->where('code', $code)->exists();
        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Coupon code already exists'
            ]);
        }

        if ($request->usage_type === 'multiple' && (!$request->usage_limit || $request->usage_limit <= 0)) {
            return response()->json([
                'status' => false,
                'message' => 'Usage limit required for multiple use coupon.'
            ]);
        }

        Coupon::create([
            'organization_id' => $orgId,
            'code' => $code,
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'min_amount' => $request->min_amount,
            'max_discount' => $request->max_discount,
            'usage_type' => $request->usage_type,
            'usage_limit' => $request->usage_type === 'multiple' ? $request->usage_limit : 1,
            'used_count' => 0,
            'start_at' => $request->start_at,
            'end_at' => $request->end_at,
            'status' => $request->status,
            'created_by' => $user->id ?? null,
        ]);

        return response()->json([
            'status' => true,
            'message' => "Coupon created!"
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {

        // ✅ Laravel validation errors JSON
        $firstError = collect($e->errors())->flatten()->first();

        return response()->json([
            'status' => false,
            'message' => $firstError,
            'errors' => $e->errors()
        ], 422);
    }
}
   // -------------------- EDIT --------------------
    public function edit($id)
    {
        $orgId = $this->getOrgId();

        $coupon = Coupon::where('organization_id', $orgId)->where('id', $id)->firstOrFail();

        return view('FrontEnd.coupons.edit', compact('coupon'));
    }

    // -------------------- UPDATE --------------------
  public function update(Request $request, $id)
{
    $user = Sentinel::check();
    $orgId = $this->getOrgId();

    $coupon = Coupon::where('organization_id', $orgId)->where('id', $id)->firstOrFail();

    $validator = \Validator::make($request->all(), [
        'name' => 'nullable|string|max:100',
        'type' => 'required|in:flat,percent',
        'value' => 'required|numeric|min:0',

        'min_amount' => 'nullable|numeric|min:0',
        'max_discount' => 'nullable|numeric|min:0',

        'usage_type' => 'required|in:single,multiple',
        'usage_limit' => 'nullable|integer|min:1|max:999999',

        'start_at' => 'nullable|date',
        'end_at' => 'nullable|date',

        'status' => 'required|integer|in:0,1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => $validator->errors()->first(),
            'errors' => $validator->errors()
        ]);
    }

    $coupon->update([
        'name' => $request->name,
        'type' => $request->type,
        'value' => $request->value,
        'min_amount' => $request->min_amount,
        'max_discount' => $request->max_discount,
        'usage_type' => $request->usage_type,
        'usage_limit' => $request->usage_type === 'multiple' ? $request->usage_limit : 1,
        'start_at' => $request->start_at,
        'end_at' => $request->end_at,
        'status' => $request->status,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Coupon updated successfully!',
        'redirect' => route('coupons.index')
    ]);
}

    // -------------------- DELETE --------------------
    public function destroy($id)
    {
        $orgId = $this->getOrgId();

        $coupon = Coupon::where('organization_id', $orgId)->where('id', $id)->firstOrFail();
        $coupon->delete();

        return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }

    // -------------------- APPLY COUPON (Payment Page) --------------------
    public function applyCoupon(Request $request)
    {
        $user = Sentinel::check();
        $orgId = $this->getOrgId();

        $request->validate([
            'code' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'lead_id' => 'nullable|integer',
            'application_id' => 'nullable|integer',
        ]);

        $code = strtoupper(trim($request->code));
        $amount = (float)$request->amount;

        $coupon = Coupon::where('organization_id', $orgId)
            ->where('code', $code)
            ->where('status', 1)
            ->first();

        if (!$coupon) {
            return response()->json(['status' => 'error', 'message' => 'Invalid coupon'], 400);
        }

        // date check
        if ($coupon->start_at && now()->lt($coupon->start_at)) {
            return response()->json(['status' => 'error', 'message' => 'Coupon not started yet'], 400);
        }

        if ($coupon->end_at && now()->gt($coupon->end_at)) {
            return response()->json(['status' => 'error', 'message' => 'Coupon expired'], 400);
        }

        // min amount
        if ($coupon->min_amount && $amount < $coupon->min_amount) {
            return response()->json([
                'status' => 'error',
                'message' => "Minimum amount required: ₹{$coupon->min_amount}"
            ], 400);
        }

        // usage check
        if ($coupon->usage_type === 'single') {
            if ($coupon->used_count >= 1) {
                return response()->json(['status' => 'error', 'message' => 'Coupon already used'], 400);
            }
        } else {
            if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                return response()->json(['status' => 'error', 'message' => 'Coupon usage limit reached'], 400);
            }
        }

        // calculate discount
        $discount = 0;

        if ($coupon->type === 'flat') {
            $discount = (float)$coupon->value;
        } else {
            $discount = ($amount * (float)$coupon->value) / 100;
        }

        if ($coupon->max_discount && $discount > (float)$coupon->max_discount) {
            $discount = (float)$coupon->max_discount;
        }

        if ($discount > $amount) $discount = $amount;

        $finalAmount = $amount - $discount;

        return response()->json([
            'status' => 'success',
            'message' => 'Coupon applied',
            'coupon_id' => $coupon->id,
            'discount' => round($discount, 2),
            'final_amount' => round($finalAmount, 2),
        ]);
    }

    // -------------------- USAGE LOGS --------------------
    public function usageLogs($id)
    {
        $orgId = $this->getOrgId();

        $coupon = Coupon::where('organization_id', $orgId)->where('id', $id)->firstOrFail();

        $logs = CouponUsage::where('organization_id', $orgId)
            ->where('coupon_id', $coupon->id)
            ->orderBy('id', 'DESC')
            ->paginate(50);

        return view('FrontEnd.coupons.usage_logs', compact('coupon', 'logs'));
    }
}
