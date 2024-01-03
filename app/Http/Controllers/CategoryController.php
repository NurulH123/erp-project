<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $categories = $user->company->categories;

        return response()->json([
            'status' => 'success',
            'data' => $categories,
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
        $categories = $company->categories()->create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil ditambahkan',
            'data'  => $categories,
        ]);
    }

    public function show(Category $category) 
    {
        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function update(Request $request, Category $category)
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

        $updCategory = Category::find($category->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Berhasil Diubah',
            'data'  => $updCategory,
        ]);
    }

    public function changeStatus(Category $category)
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
