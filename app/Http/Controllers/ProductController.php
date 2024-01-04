<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $products = $user->company->products;

        return response()->json([
            'status' => 'success', 
            'data' => $products
        ]);

    }

    public function show(Product $product)
    {
        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

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
            $filename = date('Ymd').'.'.$file->getClientOriginalExtension();
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
        // $validator = Validator::make($request->all(), [
        //     'name' => 'required',
        //     'unit_id' => 'required',
        //     'price' => 'required',
        //     'category_id' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => 'failed',
        //         'message' => $validator->errors()
        //     ]);
        // }

        $data = $request->all();

        if ($request->hasFile('photo')) {
            if (!is_null($product->photo)) {
                unlink('uploads/photo/product/'.$product->photo);
            }

            $file = $request->file('photo');
            $filename = date('Ymd').'.'.$file->getClientOriginalExtension();
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
