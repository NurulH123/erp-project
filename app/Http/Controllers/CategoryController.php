<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function indexCategory()
    {

        $category = Category::OrderBy('id', 'desc')->get();

        $data = [];
        foreach ($category as $catego) {
            $catego['item_id'] = "item[$catego->id][category_id]";
            $catego['item_quantity'] = "item[$catego->id][quantity]";

            $data[] = $catego;
        }

        return response()->json(['message' => 'category berhasil ditampilkan', 'data' => $category], 200);
    }

    public function addCategory()
    {

        $category = Category::where('category_status', 'enabled')->get();

        $data = [];
        foreach ($category as $catego) {
            $catego['item_id'] = "item[$catego->id][category_id]";
            $catego['item_quantity'] = "item[$catego->id][quantity]";

            $data[] = $catego;
        }

        return response()->json(['message' => 'category berhasil ditampilkan', 'data' => $category], 200);
    }

    public function oneCategory($id)
    {

        $data = Category::where('id', $id)->first();

        return response()->json(['message' => 'success', 'data' => $data], 200);
    }

    public function createCategory(Request $request)
    {

        $data = $request->validate([
            'name' => 'required|max:255',
        ]);

        $data['tag'] = strtolower($request->name);

        if ($request->description) {
            $data['description'] = $request->description;
        }

        $category = Category::create($data);

        return response()->json(['message' => 'category berhasil ditambahkan', 'data' => $category], 200);
    }

    public function updateCategory(Request $request, $id)
    {

        $category = Category::where('id', $id)->first();

        $data = $request->validate([
            'name' => 'required|max:255',
            'tag' => 'required|max:255',
            'category_status' => 'required',
        ]);

        if ($request->description) {
            $data['description'] = $request->description;
        }

        $category->update($data);

        return response()->json(['message' => 'category berhasil diperbaharui'], 200);

    }

    public function deleteCategory(Request $request, $id)
    {

        $category = Category::where('id', $id)->first();
        if (!$category) {
            return response()->json(['message' => 'nggak ada boss'], 200);
        }

        $category = Category::destroy($id);

        return response()->json(['message' => 'category berhasil dihapus'], 200);
    }
}
