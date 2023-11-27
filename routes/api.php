<?php

use App\Models\OrderPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SendingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DasboardController;
use App\Http\Controllers\DataLeadController;
use App\Http\Controllers\ExportDataController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\OrderPhotoController;
use App\Http\Controllers\OrderReportController;
use App\Http\Controllers\RajaOngkirController;
use App\Http\Controllers\ReturnOrderController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');


Route::post('index_profile', [ProfileController::class, 'indexProfile'])->middleware('auth:api');
Route::post('update_profile', [ProfileController::class, 'updateProfile'])->middleware('auth:api');
Route::post('one_user/{id}', [ProfileController::class, 'oneProfile'])->middleware('auth:api');
Route::post('update_user/{id}', [ProfileController::class, 'updateUser'])->middleware('auth:api');
Route::post('delete_user/{id}', [ProfileController::class, 'deleteProfile'])->middleware('auth:api');
Route::post('index_users', [ProfileController::class, 'allProfile'])->middleware('auth:api');


Route::get('index_role', [RoleController::class, 'indexRole'])->middleware('auth:api');
Route::post('create_role', [RoleController::class, 'createRole'])->middleware('auth:api');
Route::post('update_role/{id}', [RoleController::class, 'updateRole'])->middleware('auth:api');
Route::post('delete_role/{id}', [RoleController::class, 'deleteRole'])->middleware('auth:api');
Route::post('one_role/{id}', [RoleController::class, 'oneRole'])->middleware('auth:api');


Route::get('index_customer', [CustomerController::class, 'indexCustomer'])->middleware('auth:api');
Route::post('create_customer', [CustomerController::class, 'createCustomer'])->middleware('auth:api');
Route::post('delete_customer/{id}', [CustomerController::class, 'deleteCustomer'])->middleware('auth:api');
Route::post('one_customer/{id}', [CustomerController::class, 'oneCustomer'])->middleware('auth:api');
Route::post('search_customer', [CustomerController::class, 'searchCustomer'])->middleware('auth:api');


Route::get('index_product', [ProductController::class, 'indexProduct'])->middleware('auth:api');
Route::get('add_product', [ProductController::class, 'addProduct'])->middleware('auth:api');
Route::post('create_product', [ProductController::class, 'createProduct'])->middleware('auth:api');
Route::post('update_product/{id}', [ProductController::class, 'updateProduct'])->middleware('auth:api');
Route::post('delete_product/{id}', [ProductController::class, 'deleteProduct'])->middleware('auth:api');
Route::post('one_product/{id}', [ProductController::class, 'oneProduct'])->middleware('auth:api');
Route::post('search_product', [ProductController::class, 'searchProduct'])->middleware('auth:api');


Route::get('index_payment', [PaymentController::class, 'indexPayment'])->middleware('auth:api');
Route::post('create_payment', [PaymentController::class, 'createPayment'])->middleware('auth:api');
Route::post('update_payment/{id}', [PaymentController::class, 'updatePayment'])->middleware('auth:api');
Route::post('delete_payment/{id}', [PaymentController::class, 'deletePayment'])->middleware('auth:api');
Route::post('one_payment/{id}', [PaymentController::class, 'onePayment'])->middleware('auth:api');


Route::get('index_sender', [SendingController::class, 'indexSender'])->middleware('auth:api');
Route::post('create_sender', [SendingController::class, 'createSender'])->middleware('auth:api');
Route::post('update_sender/{id}', [SendingController::class, 'updateSender'])->middleware('auth:api');
Route::post('delete_sender/{id}', [SendingController::class, 'deleteSender'])->middleware('auth:api');
Route::post('one_sender/{id}', [SendingController::class, 'oneSender'])->middleware('auth:api');


Route::get('index_category', [CategoryController::class, 'indexCategory'])->middleware('auth:api');
Route::get('add_category', [CategoryController::class, 'addCategory'])->middleware('auth:api');
Route::post('create_category', [CategoryController::class, 'createCategory'])->middleware('auth:api');
Route::post('update_category/{id}', [CategoryController::class, 'updateCategory'])->middleware('auth:api');
Route::post('delete_category/{id}', [CategoryController::class, 'deleteCategory'])->middleware('auth:api');
Route::post('one_category/{id}', [CategoryController::class, 'oneCategory'])->middleware('auth:api');


Route::get('index_address', [AddressController::class, 'indexAddress'])->middleware('auth:api');
Route::get('search_address', [AddressController::class, 'searchAddress'])->middleware('auth:api');
Route::post('create_address/{customer_id}', [AddressController::class, 'createAddress'])->middleware('auth:api');


Route::get('index_coupon', [CouponController::class, 'indexCoupon'])->middleware('auth:api');
Route::post('create_coupon', [CouponController::class, 'createCoupon'])->middleware('auth:api');
Route::post('update_coupon/{id}', [CouponController::class, 'updateCoupon'])->middleware('auth:api');
Route::post('delete_coupon/{id}', [CouponController::class, 'deleteCoupon'])->middleware('auth:api');
Route::post('one_coupon/{id}', [CouponController::class, 'oneCoupon'])->middleware('auth:api');


Route::get('index_order', [OrderController::class, 'indexOrder'])->middleware('auth:api');
Route::get('index_order/{page}', [OrderController::class, 'indexOrderPaginate'])->middleware('auth:api');
Route::post('create_order', [OrderController::class, 'createOrder'])->middleware('auth:api');
Route::post('update_order/{id}', [OrderController::class, 'updateOrder'])->middleware('auth:api');
Route::post('delete_order/{id}', [OrderController::class, 'deleteOrder'])->middleware('auth:api');
Route::post('one_order/{id}', [OrderController::class, 'oneOrder'])->middleware('auth:api');
Route::post('update_status_order', [OrderController::class, 'updateStatusOrder'])->middleware('auth:api');
Route::post('search_order', [OrderController::class, 'searchOrder']);
Route::post('search_filter_order/{page}', [OrderController::class, 'searchOrderPaginate'])->middleware('auth:api');
Route::post('shipping_print', [OrderController::class, 'printShippingOrder'])->middleware('auth:api');
Route::post('send_order', [OrderController::class, 'packingStatusOrder'])->middleware('auth:api');
Route::post('update_resi/{id}', [OrderController::class, 'updateNomorResi'])->middleware('auth:api');


Route::post('report_chart_sales_dashboard/{arg}', [DasboardController::class, 'salesReportDashboard'])->middleware('auth:api');
Route::post('report_cs_dashboard/{arg}', [DasboardController::class, 'csReportDashboard'])->middleware('auth:api');
Route::post('report_dashboard', [DasboardController::class, 'reportDashboard'])->middleware('auth:api');


Route::get('index_return', [ReturnOrderController::class, 'indexReturnOrder'])->middleware('auth:api');
Route::post('create_return', [ReturnOrderController::class, 'createReturnOrder'])->middleware('auth:api');
Route::post('update_return/{id}', [ReturnOrderController::class, 'updateReturnOrder'])->middleware('auth:api');
Route::post('delete_return/{id}', [ReturnOrderController::class, 'deleteReturnOrder'])->middleware('auth:api');
Route::post('one_return/{id}', [ReturnOrderController::class, 'oneReturnOrder'])->middleware('auth:api');


Route::post('check_order', [OrderPhotoController::class, 'checkOrderPhoto']);
Route::post('upload_order_photo', [OrderPhotoController::class, 'uploadOrderPhoto']);
Route::post('get_order_photo/{id}', [OrderPhotoController::class, 'getOrderPhoto'])->middleware('auth:api');
Route::post('download_order_photo', [OrderPhotoController::class, 'downloadOrderPhoto'])->middleware('auth:api');
Route::post('delete_order_photo/{id}', [OrderPhotoController::class, 'deleteOrderPhoto'])->middleware('auth:api');



Route::post('download_barcode', [BarcodeController::class, 'generateBarcode'])->middleware('auth:api');



Route::get('province_rajaongkir', [RajaOngkirController::class, 'province']);
Route::get('city_rajaongkir/{id}', [RajaOngkirController::class, 'city']);
Route::get('subdistrict_rajaongkir/{id}', [RajaOngkirController::class, 'subdistrict']);
Route::post('cost_rajaongkir', [RajaOngkirController::class, 'cost']);


Route::post('add_data_lead', [DataLeadController::class, 'create'])->middleware('auth:api');
Route::post('delete_lead/{id}', [DataLeadController::class, 'destroy'])->middleware('auth:api');

Route::get('order_history/{id}', [HistoryController::class, 'orderHistory'])->middleware('auth:api');




Route::get('sales', [ExportDataController::class, 'sales'])->middleware('auth:api');
Route::get('index_sales', [ExportDataController::class, 'indexSales'])->middleware('auth:api');
Route::get('index_sales/{page}', [ExportDataController::class, 'indexSalesPaginate'])->middleware('auth:api');
Route::post('filter_sales/{page}', [ExportDataController::class, 'filterSalesPaginate'])->middleware('auth:api');



Route::post('leads', [ExportDataController::class, 'leads'])->middleware('auth:api');
Route::get('index_leads', [ExportDataController::class, 'indexLeads'])->middleware('auth:api');
Route::get('index_leads/{page}', [ExportDataController::class, 'indexLeadsPaginate'])->middleware('auth:api');



Route::get('frames', [ExportDataController::class, 'frames'])->middleware('auth:api');
Route::get('index_frames', [ExportDataController::class, 'indexFrames'])->middleware('auth:api');
Route::get('index_frames/{page}', [ExportDataController::class, 'indexFramesPaginate'])->middleware('auth:api');
Route::post('filter_frames/{page}', [ExportDataController::class, 'filterFramesPaginate'])->middleware('auth:api');




Route::post('order_report', [ExportDataController::class, 'orderReport'])->middleware('auth:api');
Route::get('index_order_report', [ExportDataController::class, 'indexOrderReport'])->middleware('auth:api');
Route::get('index_order_report/{page}', [ExportDataController::class, 'indexOrderReportPaginate'])->middleware('auth:api');



Route::get('resi_report', [ExportDataController::class, 'resiReport'])->middleware('auth:api');
Route::get('index_resi_report', [ExportDataController::class, 'indexResiReport'])->middleware('auth:api');
Route::get('index_resi_report/{page}', [ExportDataController::class, 'indexResiReportPaginate'])->middleware('auth:api');



Route::post('summary', [ExportDataController::class, 'summary'])->middleware('auth:api');
Route::get('index_shipping_report', [ExportDataController::class, 'IndexShippingReport'])->middleware('auth:api');
Route::get('index_shipping_report/{page}', [ExportDataController::class, 'IndexShippingReportPaginate'])->middleware('auth:api');



Route::get('index_customer_order_report', [ExportDataController::class, 'IndexCustomerOrderReport'])->middleware('auth:api');
Route::get('index_customer_order_report/{page}', [ExportDataController::class, 'IndexCustomerOrderReportPaginate'])->middleware('auth:api');



Route::get('customer_order_report', [ExportDataController::class, 'customerOrderReport'])->middleware('auth:api');
Route::get('shipping_report', [ExportDataController::class, 'ShippingReport'])->middleware('auth:api');
Route::get('customer_phone', [ExportDataController::class, 'CustomerPhone'])->middleware('auth:api');
Route::get('product_report', [ExportDataController::class, 'ProductReport'])->middleware('auth:api');
Route::get('detail_product_report/{id}', [ExportDataController::class, 'DetailProductReport'])->middleware('auth:api');

//aldimas
// Route::post('order_report',[OrderReportController::class,'order_report'])->middleware('auth:api');
// Route::get('cor',[ExportDataController::class,'cor'])->middleware('auth:api');





