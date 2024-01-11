<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkOrderController extends Controller
{
    public function addWorkOrder(Request $request, Warehouse $warehouse)
    {
        $productions = collect($request->good_productions);
        
        foreach ($productions as $item) {
            
            if (empty($item['product_id']) || empty($item['production'])) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Produk atau Stok Tidak Boleh Kosong'
                ]);
            }

            $product = Product::find($item['product_id']);
            $production = $item['production'];
            $materials = collect($product->materials);


            // ================================================================
            // |========= PROSES PENGURANGAN BAHAN MATERIAL DIGUDANG =========|
            // ================================================================
            $stocksMaterial = [];

            foreach ($materials as $material) {
                $findMaterialInWarehouse = $warehouse->products->find($material->id);
                
                if (!is_null($findMaterialInWarehouse)) {
                    $stockInWarehouse = $findMaterialInWarehouse->pivot->stock; // Stok digudang saat ini
                    $materialNeed = $material->pivot->need * $production; // Banyaknya bahan material yg dibutuhkan
                    $currentStock = $stockInWarehouse - $materialNeed; // Stok digudang saat ini, setelah mengalami pengurangan akibat dipakai

                    // Validasi stok material digudang
                    if ($currentStock < 0) return response()->json(['status' => 'failed', 'message' => 'Stok '.$material->name.' Tidak Mencukupi']);
                    
                    array_push($stocksMaterial, [
                        'material_id'   => $material->id,
                        'currentStock'  => $currentStock
                    ]);

                } else {
                    // Validasi jika barang material tidak ada digudang
                    return response()->json(['status' => 'failed', 'message' => 'Bahan Material '.$material->name.' Tidak Ada Di '.$warehouse->name]);
                }
            }

            $stocks = collect($stocksMaterial);

            // Proses pengurangan bahan material digudang. Karena digunakan untuk memproduksi barang/produk
            $stocks->each(function($item) use($warehouse) {
                // Update stok material digudang setelah dipakai
                $warehouse->products()->updateExistingPivot($item['material_id'], ['stock' => $item['currentStock']]);
            });
            


            // ========================================================
            // |========= PROSES PENAMBAHAN PRODUK DIGUDANG ==========|
            // ========================================================

            $findProductInWarehouse = $warehouse->products->find($item['product_id']);

            // Proses penambahan produk didalam gudang
            if (!is_null($findProductInWarehouse)) {
                $stockInWarehouse = $findProductInWarehouse->pivot->stock;
                $currentStock = $stockInWarehouse + $production;

                // Proses penambahan jika sebelumnya sudah ada produk
                $warehouse->products()->updateExistingPivot($item['product_id'], ['stock' => $currentStock]);
            } else {
                // Proses penambahan jika sebelumnya belum ada produk
                $warehouse->products()->attach($item['product_id'], ['stock' => $production]);
            }

            // Created work order
            WorkOrder::create($item);
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Produksi Barang Berhasil'
        ]);
    }
}
