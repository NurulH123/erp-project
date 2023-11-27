<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function indexProduct()
    {
        $product = Product::with('category')->OrderBy('id', 'desc')->get();

        $data = [];
        foreach ($product as $prod) {
            $prod['item_id'] = "item[$prod->id][product_id]";
            $prod['item_quantity'] = "item[$prod->id][quantity]";

            $data[] = $prod;
        }

        return response()->json(['message' => 'product berhasil ditambahkan', 'data' => $product], 200);

    }

    public function addProduct()
    {
        $product = Product::where('product_status', 'enabled')->with('category')->get();

        $data = [];
        foreach ($product as $prod) {
            $prod['item_id'] = "item[$prod->id][product_id]";
            $prod['item_quantity'] = "item[$prod->id][quantity]";

            $data[] = $prod;
        }

        return response()->json(['message' => 'product berhasil ditambahkan', 'data' => $product], 200);

    }

    public function oneProduct($id)
    {
        $product = Product::where('id', $id)->first();

        // Product

        $category = ProductCategory::where('product_id', $product->id)->with('category')->get();

        $category_out = [];
        foreach ($category as $frame) {
            $category_in['product_id'] = $frame->category->id;
            $category_in['name'] = $frame->category->name;
            $category_in['quantity'] = $frame->quantity;
            $category_in['item_id'] = "item[" . $frame->category->id . "][category_id]";
            $category_in['item_quantity'] = "item[" . $frame->category->id . "][quantity]";

            $category_out[] = $category_in;
        }

        $product['category'] = $category_out;

        return response()->json(['message' => 'success', 'data' => $product], 200);

    }

    public function createProduct(Request $request)
    {
        if ($request->file('image')) {
            $data = $request->validate([
                'name' => 'required|max:255',
                'price' => 'required',
                'model' => 'required',
                'quantity' => 'required',
                'product_status' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            if ($request->file('image')->isValid()) {

                $photoPath = $request->file('image')->store('product_photos', 'public');

                $data['image'] = $photoPath;
            }
        } else {
            $data = $request->validate([
                'name' => 'required|max:255',
                'model' => 'required',
                'price' => 'required',
                'quantity' => 'required',
                'product_status' => 'required',
            ]);
        }


        $tag = strtolower($request->name);
        $data['tag'] = $tag;

        if ($request->description) {
            $data['description'] = $request->description;
        }

        if ($request->weight) {
            $data['weight'] = $request->weight;
        }

        if ($request->weight_class) {
            $data['weight_class'] = $request->weight_class;
        }

        if ($request->minimum_quantity) {
            $data['minimum_quantity'] = $request->minimum_quantity;
        }

        $product = Product::create($data);

        $product_id = $product->id;

        $frame['product_id'] = $product_id;

        $categories = $request->category;
        foreach ($categories as $category) {
            $frame['category_id'] = $category['category_id'];
            $frame['quantity'] = $category['quantity'];

            ProductCategory::create($frame);
        }

        return response()->json(['message' => 'product berhasil ditambahkan', 'data' => $product], 200);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response()->json(['message' => 'product belum ditambahkan'], 403);
        }

        if (!$request->file('image')) {
            if ($request->image != null) {
                $data = $request->validate([
                    'name' => 'required|max:255',
                    'model' => 'required',
                    'price' => 'required',
                    'quantity' => 'required',
                    'product_status' => 'required',
                    'image' => 'required',
                ]);

            } else {

                $data = $request->validate([
                    'name' => 'required|max:255',
                    'model' => 'required',
                    'price' => 'required',
                    'quantity' => 'required',
                    'product_status' => 'required',
                ]);

                $data['image'] = null;

            }

        } elseif ($request->file('image')) {
            $data = $request->validate([
                'name' => 'required|max:255',
                'model' => 'required',
                'price' => 'required',
                'quantity' => 'required',
                'product_status' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',

            ]);

            if ($request->file('image')->isValid()) {

                $photoPath = $request->file('image')->store('product_photos', 'public');

                $data['image'] = $photoPath;
            }
        }

        if ($request->tag) {
            $data['tag'] = $request->tag;
        }

        if ($request->description) {
            $data['description'] = $request->description;
        }

        if ($request->minimum_quantity) {
            $data['minimum_quantity'] = $request->minimum_quantity;
        }

        if ($request->weight) {
            $data['weight'] = $request->weight;
        }

        if ($request->weight_class) {
            $data['weight_class'] = $request->weight_class;
        }

        $product->update($data);

        $product_id = $product->id;

        $frame['product_id'] = $product_id;

        $categories = $request->category;

        $deleteProductCategory = ProductCategory::where('product_id', $product_id)->delete();

        foreach ($categories as $category) {
            $frame['category_id'] = $category['category_id'];
            $frame['quantity'] = $category['quantity'];

            ProductCategory::create($frame);
        }

        return response()->json(['message' => 'product berhasil diperbaharui', 'data' => $product], 200);
    }

    public function deleteProduct(Request $request, $id)
    {

        $product = Product::where('id', $id)->first();

        if (!$product) {
            return response()->json(['message' => 'product belum terdaftar'], 403);
        }

        ProductCategory::where('product_id', $product->id)->delete();

        if ($product->image != null) {
            if (Storage::disk('public')->exists($product->image)) {
                // Hapus foto dari penyimpanan
                Storage::disk('public')->delete($product->image);

            }
        }

        $product = Product::destroy($id);

        return response()->json(['message' => 'product berhasil dihapus'], 200);
    }


    public function searchProduct(Request $request)
    {

        $product = Product::where('name', 'like', '%' . $request->product . '%')->get();

        return response()->json(['data' => $product], 200);

    }
}
