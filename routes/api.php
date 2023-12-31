<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SendingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\EmployeeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DasboardController;
use App\Http\Controllers\DataLeadController;
use App\Http\Controllers\ExportDataController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\OrderPhotoController;
use App\Http\Controllers\RajaOngkirController;
use App\Http\Controllers\ReturnOrderController;
use App\Http\Controllers\Company\PositionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function() {

    Route::prefix('position')->group(function() {
        Route::get('all', [PositionController::class, 'index']);
        Route::post('create', [PositionController::class, 'create']);
        Route::patch('update/{id}', [PositionController::class, 'update']);
        Route::get('status/{id}', [PositionController::class, 'index'])->name('position.change-status');
    });

    Route::prefix('profile')->group(function() {
        Route::post('/', [ProfileController::class, 'index']);
        Route::patch('update', [ProfileController::class, 'update']);
        Route::post('one_user/{id}', [ProfileController::class, 'oneProfile']);
        Route::post('update_user/{id}', [ProfileController::class, 'updateUser']);
        Route::post('delete_user/{id}', [ProfileController::class, 'delete']);
        Route::post('index_users', [ProfileController::class, 'all']);
    });

    Route::group(
        ['prefix' => 'company'],
        function() {
            Route::get('/', [CompanyController::class, 'index']);
            Route::post('/', [CompanyController::class, 'create']);
            Route::patch('/{id}', [CompanyController::class, 'update']);
            Route::delete('/{company}', [CompanyController::class, 'destroy']);
            Route::patch('/{id}/change-status', [CompanyController::class, 'changeStatus']);
        });

    Route::group(
        ['prefix' => 'employee'],
        function()  {
            Route::post('/', [EmployeeController::class, 'create']);
        }
    );

    Route::group(
        ['prefix' => 'role'],
        function() {
            Route::get('/', [RoleController::class, 'index']);
            Route::get('/{role}/show', [RoleController::class, 'show']);
            Route::post('/{id}/change-status', [RoleController::class, 'changeStatus']);
            Route::post('/', [RoleController::class, 'create']);
            Route::patch('{id}', [RoleController::class, 'update']);
            Route::delete('/{id}', [RoleController::class, 'destroy']);
    });

    Route::prefix('customer')->group(function() {
        Route::get('index', [CustomerController::class, 'indexCustomer']);
        Route::post('create', [CustomerController::class, 'createCustomer']);
        Route::post('delete/{id}', [CustomerController::class, 'deleteCustomer']);
        Route::post('one/{id}', [CustomerController::class, 'oneCustomer']);
        Route::post('search', [CustomerController::class, 'searchCustomer']);
    });

    Route::prefix('product', function() {
        Route::get('index', [ProductController::class, 'indexProduct']);
        Route::get('add', [ProductController::class, 'addProduct']);
        Route::post('create', [ProductController::class, 'createProduct']);
        Route::post('update/{id}', [ProductController::class, 'updateProduct']);
        Route::post('delete/{id}', [ProductController::class, 'deleteProduct']);
        Route::post('one/{id}', [ProductController::class, 'oneProduct']);
        Route::post('search', [ProductController::class, 'searchProduct']);
    });

    Route::prefix('payment')->group(function() {
        Route::get('index', [PaymentController::class, 'indexPayment']);
        Route::post('create', [PaymentController::class, 'createPayment']);
        Route::post('update/{id}', [PaymentController::class, 'updatePayment']);
        Route::post('delete/{id}', [PaymentController::class, 'deletePayment']);
        Route::post('one/{id}', [PaymentController::class, 'onePayment']);
    });

    Route::prefix('sender')->group(function() {
        Route::get('index', [SendingController::class, 'indexSender']);
        Route::post('create', [SendingController::class, 'createSender']);
        Route::post('update/{id}', [SendingController::class, 'updateSender']);
        Route::post('delete/{id}', [SendingController::class, 'deleteSender']);
        Route::post('one/{id}', [SendingController::class, 'oneSender']);
    });

    Route::prefix('category')->group(function() {
        Route::get('index', [CategoryController::class, 'indexCategory']);
        Route::get('add', [CategoryController::class, 'addCategory']);
        Route::post('create', [CategoryController::class, 'createCategory']);
        Route::post('update/{id}', [CategoryController::class, 'updateCategory']);
        Route::post('delete/{id}', [CategoryController::class, 'deleteCategory']);
        Route::post('one/{id}', [CategoryController::class, 'oneCategory']);
    });

    Route::prefix('address')->group(function() {
        Route::get('index', [AddressController::class, 'indexAddress']);
        Route::get('search', [AddressController::class, 'searchAddress']);
        Route::post('create/{customer_id}', [AddressController::class, 'createAddress']);
    });


    Route::prefix('coupon')->group(function() {
        Route::get('index', [CouponController::class, 'indexCoupon']);
        Route::post('create', [CouponController::class, 'createCoupon']);
        Route::post('update/{id}', [CouponController::class, 'updateCoupon']);
        Route::post('delete/{id}', [CouponController::class, 'deleteCoupon']);
        Route::post('one/{id}', [CouponController::class, 'oneCoupon']);
    });

    Route::prefix('order')->group(function() {
        Route::get('index', [OrderController::class, 'indexOrder']);
        Route::get('index/{page}', [OrderController::class, 'indexOrderPaginate']);
        Route::post('create', [OrderController::class, 'createOrder']);
        Route::post('update/{id}', [OrderController::class, 'updateOrder']);
        Route::post('delete/{id}', [OrderController::class, 'deleteOrder']);
        Route::post('one/{id}', [OrderController::class, 'oneOrder']);
        Route::post('update_status', [OrderController::class, 'updateStatusOrder']);
        Route::post('search', [OrderController::class, 'searchOrder']);
        Route::post('search_filter/{page}', [OrderController::class, 'searchOrderPaginate']);
        Route::post('shipping_print', [OrderController::class, 'printShippingOrder']);
        Route::post('send', [OrderController::class, 'packingStatusOrder']);
        Route::post('update_resi/{id}', [OrderController::class, 'updateNomorResi']);
    });

    Route::prefix('report')->group(function() {
        Route::post('chart_sales_dashboard/{arg}', [DasboardController::class, 'salesReportDashboard']);
        Route::post('cs_dashboard/{arg}', [DasboardController::class, 'csReportDashboard']);
        Route::post('dashboard', [DasboardController::class, 'reportDashboard']);
    });

    Route::prefix('return')->group(function() {
        Route::get('index', [ReturnOrderController::class, 'indexReturnOrder']);
        Route::post('create', [ReturnOrderController::class, 'createReturnOrder']);
        Route::post('update/{id}', [ReturnOrderController::class, 'updateReturnOrder']);
        Route::post('delete/{id}', [ReturnOrderController::class, 'deleteReturnOrder']);
        Route::post('one/{id}', [ReturnOrderController::class, 'oneReturnOrder']);
    });


    Route::post('check_order', [OrderPhotoController::class, 'checkOrderPhoto']);
    Route::post('upload_order_photo', [OrderPhotoController::class, 'uploadOrderPhoto']);
    Route::post('get_order_photo/{id}', [OrderPhotoController::class, 'getOrderPhoto']);
    Route::post('download_order_photo', [OrderPhotoController::class, 'downloadOrderPhoto']);
    Route::post('delete_order_photo/{id}', [OrderPhotoController::class, 'deleteOrderPhoto']);



    Route::post('download_barcode', [BarcodeController::class, 'generateBarcode']);



    Route::get('province_rajaongkir', [RajaOngkirController::class, 'province']);
    Route::get('city_rajaongkir/{id}', [RajaOngkirController::class, 'city']);
    Route::get('subdistrict_rajaongkir/{id}', [RajaOngkirController::class, 'subdistrict']);
    Route::post('cost_rajaongkir', [RajaOngkirController::class, 'cost']);


    Route::post('add_data_lead', [DataLeadController::class, 'create']);
    Route::post('delete_lead/{id}', [DataLeadController::class, 'destroy']);

    Route::get('order_history/{id}', [HistoryController::class, 'orderHistory']);




    Route::get('sales', [ExportDataController::class, 'sales']);
    Route::get('index_sales', [ExportDataController::class, 'indexSales']);
    Route::get('index_sales/{page}', [ExportDataController::class, 'indexSalesPaginate']);
    Route::post('filter_sales/{page}', [ExportDataController::class, 'filterSalesPaginate']);



    Route::post('leads', [ExportDataController::class, 'leads']);
    Route::get('index_leads', [ExportDataController::class, 'indexLeads']);
    Route::get('index_leads/{page}', [ExportDataController::class, 'indexLeadsPaginate']);



    Route::get('frames', [ExportDataController::class, 'frames']);
    Route::get('index_frames', [ExportDataController::class, 'indexFrames']);
    Route::get('index_frames/{page}', [ExportDataController::class, 'indexFramesPaginate']);
    Route::post('filter_frames/{page}', [ExportDataController::class, 'filterFramesPaginate']);




    Route::post('order_report', [ExportDataController::class, 'orderReport']);
    Route::get('index_order_report', [ExportDataController::class, 'indexOrderReport']);
    Route::get('index_order_report/{page}', [ExportDataController::class, 'indexOrderReportPaginate']);



    Route::get('resi_report', [ExportDataController::class, 'resiReport']);
    Route::get('index_resi_report', [ExportDataController::class, 'indexResiReport']);
    Route::get('index_resi_report/{page}', [ExportDataController::class, 'indexResiReportPaginate']);



    Route::post('summary', [ExportDataController::class, 'summary']);
    Route::get('index_shipping_report', [ExportDataController::class, 'IndexShippingReport']);
    Route::get('index_shipping_report/{page}', [ExportDataController::class, 'IndexShippingReportPaginate']);



    Route::get('index_customer_order_report', [ExportDataController::class, 'IndexCustomerOrderReport']);
    Route::get('index_customer_order_report/{page}', [ExportDataController::class, 'IndexCustomerOrderReportPaginate']);


    Route::get('customer_order_report', [ExportDataController::class, 'customerOrderReport']);
    Route::get('shipping_report', [ExportDataController::class, 'ShippingReport']);
    Route::get('customer_phone', [ExportDataController::class, 'CustomerPhone']);
    Route::get('product_report', [ExportDataController::class, 'ProductReport']);
    Route::get('detail_product_report/{id}', [ExportDataController::class, 'DetailProductReport']);

//aldimas
// Route::post('order_report',[OrderReportController::class,'order_report']);
// Route::get('cor',[ExportDataController::class,'cor']);

});
