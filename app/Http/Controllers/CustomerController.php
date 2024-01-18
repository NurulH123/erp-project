<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $company = $user->company;

        return response()->json([
            'status' => 'success',
            'data' => $company->customer,
        ]);
    }

    public function show(Customer $customer)
    {
        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ]);
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'address' => 'required'
        ],[
            'name.required' => 'Nama Harus Diisi',
            'address.required' => 'Alamat Harus Diisi', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $data = $request->all();
        $customer = $user->company->customer()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Ditambahkan',
            'data' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'address' => 'required'
        ],[
            'name.required' => 'Nama Harus Diisi',
            'address.required' => 'Alamat Harus Diisi', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ], 442);
        }

        $data = $request->all();
        $customer->update($data);

        $updCustomer = Customer::find($customer->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Diubah',
            'data' => $updCustomer,
        ]);
    }

    public function changeStatus(Customer $customer)
    {
        $status = $customer->is_active;

        $customer->update(['is_active' => !$status]);
        $IStatus = $status ? 'Aktif' : 'Tidak Aktif';

        return response()->json([
            'status' => 'success', 
            'is_status' => $status,
            'message' => 'Vendor '.$IStatus
        ]);
    }
}
