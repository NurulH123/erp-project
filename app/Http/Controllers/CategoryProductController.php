<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryProduct;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;

class CategoryProductController extends Controller
{
    public function index()
    {
        $sort = request('sort') ?? '5';

        $productCategories = CategoryProduct::whereHas('company', function(Builder $query) {
            $user = auth()->user();
            $companyId = $user->company->id;

            $query->where('id', $companyId);
        })->paginate($sort);

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
