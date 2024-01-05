<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\ProductWarehouse;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductWarehouseController extends Controller
{
    public function dataProductWarehouse()
    {
        $prouctWarehouse = ProductWarehouse::with(['product', 'warehouse'])->get();

        return response()->json([
            'status' => 'success',
            'data' => $prouctWarehouse,
        ]);
    }

    public function addProductTo(Request $request, Warehouse $warehouse)
    {
        $validator = Validator::make($request->all(), 
            ['product_stock' => 'required'],
            ['product_stock.required' => 'Produk atau Stok Harus Diisi']
        );

        if ($validator->fails()) return response()->json(['status' => 'failed', 'message' => $validator->errors()],  442);
        
        $products_stock = collect($request->product_stock);

        // Proses pemindahan produk dari satu gudang ke gudang yg lain 
        $products_stock->each(function($item, $warehouse_id) use($warehouse) {

            $prevWarehouse = Warehouse::find($warehouse_id);
            $product = $prevWarehouse->products->where('id', $item['product_id'])->first();
            $stockInPrevWarehouse = $product->pivot->stock;

            $product = $warehouse->products->where('id', $item['product_id'])->first();

            // Proses pengurangan produk sebelumnya dan validasi
            $productReduction = $stockInPrevWarehouse - $item['stock'];
            if($productReduction < 0) return response()->json(['status' => 'failed', 'message' => 'Pengurangan Produk Terlalu Banyak']);
            
            // Proses penambahan stock
            if (!is_null($product)) {
                $stockPrevious = $product->pivot->stock;
                // Proses penambahan stock ketika sebelumnya, digudang sudah ada stock
                $currentStock = $stockPrevious + $item['stock'];

                $warehouse->products()->updateExistingPivot($item['product_id'], ['stock' => $currentStock]);
            } else {
                // Proses penambahan stock ketika sebelumnya, belum ada stock digudang
                $warehouse->products()->attach([$item['product_id'] => ['stock' => $item['stock']]]);
            }

            // Proses pengurangan stok di gudang sebelumnya
            $prevWarehouse->products()->updateExistingPivot($item['product_id'], ['stock' => $productReduction]);

        });

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Ditambahkan'
        ]);
    }

    public function deleteProductIn(Request $request,Warehouse $warehouse)
    {
        $validator = Validator::make($request->all(), 
            ['product_id' => 'required'],
            ['product_id.required' => 'Produk Harus Diisi']
        );

        if ($validator->fails()) return response()->json(['status' => 'failed', 'message' => $validator->errors()],  442);
        
        $warehouse->products()->detach($request->product_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data Telah Dihapus'
        ]);
    }
}
