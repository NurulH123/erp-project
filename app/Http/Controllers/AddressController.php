<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function indexAddress()
    {

        $data = Address::OrderBy('id', 'desc')->get();

        return response()->json(['message' => 'address berhasil ditampilkan', 'data' => $data], 200);
    }

    public function createAddress(Request $request, $customer_id)
    {
        $data = $request->validate([
            'full_address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'postal_code' => 'required',
        ]);

        $data['customer_id'] = $customer_id;

        $address = Address::create($data);

        return response()->json(['message' => 'address berhasil ditambahkan', 'data' => $address], 200);
    }

    public function updateAddress(Request $request, $id)
    {

    }

    public function deleteAddress(Request $request, $id)
    {
    }

    public function searchAddress(Request $request)
    {
        $address = Address::where('surename', 'like', '%' . $request->address . '%')->get();

        return response()->json(['data' => $address], 200);

    }
}
