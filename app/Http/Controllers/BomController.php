<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BomController extends Controller
{
    public function addBom(Request $request, Product $product)
    {
        $validator =  Validator::make($request->all(), 
            ['material_need' => 'required'],
            ['material_need.required' => 'Bahan-bahan harus diisi']
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        $materials = collect($request->material_need);

        // Proses memasukkan bahan baku
        $materials->each(function($item) use($product) {
            $product->materials()->attach($product->id, $item);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Material Produk Telahh Ditambahkan'
        ]);
    }
}
