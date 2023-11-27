<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Str;
use Throwable;

class CustomerController extends Controller
{
    public function indexCustomer()
    {

        $data = Customer::OrderBy('id', 'desc')->get();

        return response()->json(['message' => 'customer berhasil ditampilkan', 'data' => $data], 200);
    }

    public function oneCustomer($id)
    {
        $data = Customer::where('id', $id)->first();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function createCustomer(Request $request)
    {

        $data = $request->validate([
            'first_name' => 'required|max:255',
            'phone' => 'required',
        ]);

        if ($request->last_name) {
            $data['last_name'] = $request->last_name;
        } else {
            $data['last_name'] = $request->district;
        }

        if (substr($request->phone, 0, 2) === "08") {
            // Menghapus angka 0 pertama dan menggantinya dengan "62"
            $data['phone'] = "62" . substr($request->phone, 1);
        } else {
            $data['phone'] = $request->phone;
        }

        if ($request->email) {
            $data['email'] = $request->email;
        } else {
            $data['email'] = $data['phone'] . "@makenliving.my.id";
        }


        $data['surename'] = $request->first_name . ' ' . $request->last_name;

        if ($request->second_phone) {

            if (substr($request->second_phone, 0, 2) === "08") {
                // Menghapus angka 0 pertama dan menggantinya dengan "62"
                $data['second_phone'] = "62" . substr($request->second_phone, 1);
            } else {
                $data['phone'] = $request->second_phone;
            }

        } else {
            $data['second_phone'] = $data['phone'];
        }

        $customer = Customer::create($data);

        return $customer;
    }

    public function updateCustomer(Request $request, $id)
    {
        //////////////////////////////////////////////
    }

    public function deleteCustomer(Request $request, $id)
    {
        $customer = Customer::where('id', $id)->first();

        if (!$customer) {
            return response()->json(['message' => 'customer belum terdaftar'], 403);
        }

        Address::where('customer_id', $customer->id)->delete();

        $customer = Customer::destroy($id);

        return response()->json(['message' => 'customer berhasil dihapus'], 200);
    }

    public function searchCustomer(Request $request)
    {
        $customer = Customer::where('surename', 'like', '%' . $request->customer . '%')->get();

        return response()->json(['data' => $customer], 200);

    }

}
