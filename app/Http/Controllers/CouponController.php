<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function indexCoupon()
    {

        $coupons = Coupon::with('order')->OrderBy('id', 'desc')->get();

        // $data = [];
        foreach ($coupons as $coupon) {
            $coupon['terpakai'] = $coupon->order->count();
            $coupon['sisa'] = $coupon->coupon_uses - $coupon['terpakai'];

            // $data[] = $coupon;
        }

        return response()->json(['message' => 'coupon berhasil ditampilkan', 'data' => $coupons], 200);
    }

    public function oneCoupon($id)
    {

        $data = Coupon::where('id', $id)->first();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function createCoupon(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|min:4',
            'type' => 'required',
            'discount' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'coupon_uses' => 'required',
            'customer_uses' => 'required',
            'status' => 'required',
        ]);


        $coupon = Coupon::create($data);

        return response()->json(['message' => 'category berhasil ditambahkan', 'data' => $coupon], 200);
    }

    public function updateCoupon(Request $request, $id)
    {

        $coupon = Coupon::where('id', $id)->first();

        $data = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|min:4',
            'type' => 'required',
            'category' => 'required',
            'discount' => 'required',
            'date_start' => 'required',
            'date_end' => 'required',
            'coupon_uses' => 'required',
            'customer_uses' => 'required',
            'status' => 'required',
        ]);

        $coupon->update($data);

        return response()->json(['message' => 'coupon berhasil diperbaharui', 'data' => $coupon], 200);

    }

    public function deleteCoupon($id)
    {

        $coupon = Coupon::where('id', $id)->first();

        if (!$coupon) {
            return response()->json(['message' => 'data tidak ditemukan'], 200);
        }

        Coupon::destroy($id);

        return response()->json(['message' => 'coupon berhasil dihapus'], 200);
    }
}

