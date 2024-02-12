<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use App\Models\ProductWarehouse;
use Illuminate\Support\Facades\Validator;

class ProductWarehouseController extends Controller
{
    public function dataProductWarehouse()
    {
        $prouctWarehouse = ProductWarehouse::with(['product', 'warehouse'])
                            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $prouctWarehouse,
        ]);
    }

    public function addProductWarehouse(Request $request, Warehouse $warehouse)
    {
        $validator = Validator::make($request->all(), 
            ['product_stock' => 'required'],
            ['product_stock.required' => 'Product Harus Diisi']
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => 'failed',
                'message' => $validator->errors()
            ]);
        }

        $product_stock = collect($request->product_stock);

        // Proses penambahan produk ke gudang 
        $product_stock->each(function($item) use($warehouse) {

            // $product = $warehouse->products->where('id', $item['product_id'])->first();
            $product = $warehouse->products->find($item['product_id']);

            // Proses penambahan stock
            if (!is_null($product)) {
                $stockPrevInWarehouse = $product->pivot->stock;

                // Proses penambahan stock ketika sebelumnya, digudang sudah ada stock
                $currentStock = $stockPrevInWarehouse + $item['stock'];

                $warehouse->products()->updateExistingPivot($item['product_id'], ['stock' => $currentStock]);
            } else {
                // Proses penambahan stock ketika sebelumnya, belum ada stock digudang
                $warehouse->products()->attach([$item['product_id'] => ['stock' => $item['stock']]]);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Produk Telah Ditambahkan',
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
        $products_stock->each(function($item) use($warehouse) {

            $prevWarehouse = Warehouse::find($item['warehouse_id']); // warehouse yg ingin dipindahhkan produknya ke warehouse lain
            $product = $prevWarehouse->products->find($item['product_id']);
            $stockInPrevWarehouse = $product->pivot->stock;

            $product = $warehouse->products->find($item['product_id']);

            // Proses pengurangan produk sebelumnya dan validasi
            $productReduction = $stockInPrevWarehouse - $item['stock'];
            if($productReduction < 0) return response()->json(['status' => 'failed', 'message' => 'Pengurangan Produk Terlalu Banyak']);
            
            // Proses penambahan stock
            if (!is_null($product)) {
                $stockPrevInWarehouse = $product->pivot->stock;

                // Proses penambahan stock ketika sebelumnya, digudang sudah ada stock
                $currentStock = $stockPrevInWarehouse + $item['stock'];

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
            'message' => 'Product Telah Dipindahkan ke '.$warehouse->name
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
