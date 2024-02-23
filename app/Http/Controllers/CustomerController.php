<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? '5';
        $customers = Customer::whereHas('customerable', function(Builder $query) {
                            $user = auth()->user()->employee;
                            $companyId = $user->company->id;

                            $query->where('id', $companyId);
                        })
                        ->where('is_active', true)
                        ->paginate($sort);

        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

    public function allData()
    {
        $user = auth()->user()->employee;

        return response()->json([
            'status' => 'success',
            'data' => $user->company->customers
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
        $user = auth()->user()->employee;
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
        $customer = $user->company->customers()->create($data);

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
        $status = !$customer->is_active;

        $customer->update(['is_active' => $status]);
        $IStatus = $status ? 'Aktif' : 'Tidak Aktif';

        return response()->json([
            'status' => 'success', 
            'is_status' => $status,
            'message' => 'Pelanggan '.$customer->name.' '.$IStatus
        ]);
    }
}
