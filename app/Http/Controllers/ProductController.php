<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? '5';
        $user = auth()->user()->employee;

        $products = Product::where('company_id', $user->company->id)
                        ->with(['warehouses' => function(Builder $query) use($user){
                                $query->where('company_id', $user->company->id);
                            }, 
                            'unit:id,name', 
                            'category:id,name'
                        ])
                        ->paginate($sort)->toArray();

        return response()->json([
            'status' => 'success', 
            'data' => $products
        ]);

    }

    public function allData()
    {
        $user = auth()->user()->employee;
        $products = $user->company->products->where('status', true);

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }

    public function show(Product $product)
    {
        $product = Product::with(['warehouses' => function(Builder $query) {
                        $user = auth()->user()->employee;

                        $query->where('company_id', $user->company->id);
                    }, 'unit:id,name', 'category:id,name'])
                    ->where('id', $product->id)
                    ->first();
                    
        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user()->employee;

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'unit_id' => 'required',
            'price' => 'required',
            'category_product_id' => 'required',
            'code_product' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = date('YmdHis').'.'.$file->getClientOriginalExtension();
            $file->move('uploads/photo/product', $filename);

            $data['photo'] = 'uploads/photo/product/'.$filename;
        }

        $product = $user->company->products()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Ditambahkan',
            'data' => $product,
        ]);
    }
    
    public function update(Request $request, Product $product)
    {
        $data = $request->all();

        if ($request->hasFile('photo')) {
            if (!is_null($product->photo)) {
                unlink($product->photo);
            }

            $file = $request->file('photo');
            $filename = date('YmdHis').'.'.$file->getClientOriginalExtension();
            $file->move('uploads/photo/product', $filename);

            $data['photo'] = 'uploads/photo/product/'.$filename;
        }

        $product->update($data);
        $product = Product::find($product->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data' => $product,
        ]);
    }

    public function changeStatus(Product $product)
    {
        $status  = !$product->status;
        $statusText = $status ? 'Diaktifkan' : 'Dinonaktifkan';

        $product->update(['status' => $status]);

        return response()->json([
            'status' => 'success',
            'message' => $product->name.' '.$statusText,
        ]);
    }
}
