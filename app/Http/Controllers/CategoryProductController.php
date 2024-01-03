<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $productCategories = $user->company->productCategories;

        return response()->json([
            'status' => 'success',
            'data' => $productCategories,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $company = $user->company;

        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'code'  => 'required',
        ], [
            'name.required' => 'Nama Harus Diisi',
            'code.required' => 'Kode Harus Diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 442);
        }

        $data = $request->only('name', 'code');
        $productCategories = $company->productCategories()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil ditambahkan',
            'data'  => $productCategories,
        ]);
    }

    public function show(CategoryProduct $category) 
    {
        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function update(Request $request, CategoryProduct $category)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'code'  => 'required',
        ], [
            'name.required' => 'Nama Harus Diisi',
            'code.required' => 'Kode Harus Diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors(),
            ], 442);
        }

        $data = $request->only('name', 'code');
        $category->update($data);

        $updCategory = CategoryProduct::find($category->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data'  => $updCategory,
        ]);
    }

    public function changeStatus(CategoryProduct $category)
    {
        $status = !$category->status;
        $statusText = $status ? 'Diaktifkan' : 'Dinonaktifkan';

        $category->update(['status' => $status]);

        return response()->json([
            'status' => 'success',
            'message' => $category->name.' '.$statusText
        ]);
    }
}
