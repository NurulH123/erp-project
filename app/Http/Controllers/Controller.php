<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Position;
use App\Models\ProductCategory;
use App\Models\Role;
use App\Models\StatusEmployee;
use App\Models\Unit;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function search($type)
    {
        $user = auth()->user()->employee;
        $models = [
            "customer"  => [
                "model" => "App\Models\Customer",
                "relation" => "customerable_id"
            ], // customerable
            "position"  => [
                "model" => "App\Models\Position",
                "relation" => "positionable_id"
            ], // positionable
            "category"  => [
                "model" => "App\Models\ProductCategory",
                "relation" => "company_id"
            ],
            "role"      => [
                "model" => "App\Models\Role",
                "relation" => "roleable_id"
            ], // roleable
            "status"    => [
                "model" => "App\Models\StatusEmployee",
                "relation" => "statusable_id"
            ], // statusable
            "unit"      => [
                "model" => "App\Models\Unit",
                "relation" => "company_id"
            ],
            "vendor"    => [
                "model" => "App\Models\Vendor",
                "relation" => "vendorable_id"
            ], // vendorable
            "warehouse" => [
                "model" => "App\Models\Warehouse",
                "relation" => "company_id"
            ],
        ];

        try {
            $class = $models[$type]['model'];
            $name = request('name') ?? '';
            $foreign = $models[$type]['relation'];

            $data = $class::where($foreign, $user->company->id)
                        ->where('name', 'like', "%$name%")
                        ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => "Nama Kategori $type Tidak Ada"
            ], Response::HTTP_BAD_REQUEST);
        }
    }

}
