<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminRoleController;
use App\Http\Controllers\PermissionRoleController;
use App\Http\Controllers\StatusEmployeeController;
use App\Http\Controllers\Auth\PermissionController;
use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\Company\EmployeeController;
use App\Http\Controllers\Company\PositionController;
use App\Http\Controllers\Company\BranchCompanyController;
use App\Http\Controllers\Company\CompanyPermissionController;

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

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/me', [AuthController::class, 'me']);

    Route::post('logout', [AuthController::class, 'logout']);

    // ========================================================================
    // |============================ COMPANY =================================|
    // ========================================================================
    Route::group(
        ['prefix' => 'company'],
        function() {
            Route::get('/', [CompanyController::class, 'index']);
            Route::get('/list', [CompanyController::class, 'listAll']);
            Route::post('/', [CompanyController::class, 'create']);
            Route::patch('/{id}', [CompanyController::class, 'update']);
            Route::delete('/{company}', [CompanyController::class, 'destroy']);
            Route::patch('/{id}/change-status', [CompanyController::class, 'changeStatus']);
    });

    // ========================================================================
    // |======================== COMPANY PERMISSION ==========================|
    // ========================================================================
    Route::group(
        ['prefix' => 'company/permission'],
        function() {
            Route::post('/', [CompanyPermissionController::class, 'addPermissionTo']);
            Route::patch('/update', [CompanyPermissionController::class, 'updateCompanyPermission']);
        }
    );

    // ========================================================================
    // |=============================== PERMISSION ===========================|
    // ========================================================================
    Route::group(
        ['prefix' => 'permission'],
        function() {
            Route::get('/', [PermissionController::class, 'index']);
            Route::post('/', [PermissionController::class, 'create']);
            Route::patch('{permission}/update', [PermissionController::class, 'update']);
            Route::get('/{permission}/change-status', [PermissionController::class, 'changeStatus'])->name('permission.change-status');
    });

    Route::middleware('hasCompany')->group(function() {
    // ========================================================================
    // |=============================== POSITION =============================|
    // ========================================================================
        Route::prefix('position')->group(function() {
            Route::get('/', [PositionController::class, 'index']);
            Route::post('/', [PositionController::class, 'create']);
            Route::patch('{position}/update', [PositionController::class, 'update']);
            Route::get('{position}/status/change', [PositionController::class, 'changeStatus'])->name('position.change-status');
        });
    
    // ========================================================================
    // |=============================== PROFILE ==============================|
    // ========================================================================
        Route::prefix('profile')->group(function() {
            Route::post('/', [ProfileController::class, 'index']);
            Route::patch('update', [ProfileController::class, 'update']);
            Route::post('one_user/{id}', [ProfileController::class, 'oneProfile']);
            Route::post('update_user/{id}', [ProfileController::class, 'updateUser']);
            Route::post('delete_user/{id}', [ProfileController::class, 'delete']);
            Route::post('index_users', [ProfileController::class, 'all']);
        });
    
    // ========================================================================
    // |=============================== EMPLOYEE =============================|
    // ========================================================================
        Route::group(
            ['prefix' => 'employee'],
            function()  {
                Route::get('/', [EmployeeController::class, 'index']);
                Route::get('/{id}', [EmployeeController::class, 'show']);
                Route::post('/', [EmployeeController::class, 'create']);
                Route::patch('/{employee}', [EmployeeController::class, 'update']);
                Route::get('/{employee}/change/status', [EmployeeController::class, 'changeStatus']);
                Route::post('/{employee}/change/admin', [EmployeeController::class, 'changeAdmin']);
            }
        );

    // ========================================================================
    // |========================= ROLE USER EMPLOYEE =========================|
    // ========================================================================
        Route::group(
            ['prefix' => 'employee/{employee}/role'],
            function() {
                Route::post('/add', [AdminRoleController::class, 'addRoleToAdmin']);
                Route::post('/status/change', [AdminRoleController::class, 'changeStatus']);
            }
        );
    
    // ========================================================================
    // |================================== ROLE ==============================|
    // ========================================================================
        Route::group(
            ['prefix' => 'role'],
            function() {
                Route::get('/', [RoleController::class, 'index']);
                Route::post('/', [RoleController::class, 'create']);
                Route::patch('{role}/update', [RoleController::class, 'update']);
                Route::get('/{role}/change-status', [RoleController::class, 'changeStatus'])->name('role.change-status');
        });

    // ========================================================================
    // |=========================== ROLE PERMISSION ==========================|
    // ========================================================================
        Route::group(
            ['prefix' => 'role/{role}/permission'],
            function() {
                Route::post('/', [PermissionRoleController::class, 'givePermissionTo']);
                Route::patch('/', [PermissionRoleController::class, 'updateRolePermission']);
            }
        );
    
    // ========================================================================
    // |=========================== STATUS EMPLOYEE ==========================|
    // ========================================================================
        Route::group(
            ['prefix' => 'status-employee'],
            function() {
                Route::get('/', [StatusEmployeeController::class, 'index']);
                Route::post('/', [StatusEmployeeController::class, 'create']);
                Route::patch('{status}/update', [StatusEmployeeController::class, 'update']);
                Route::get('/{employeeStatus}/change-status', [StatusEmployeeController::class, 'changeStatus']);
        });

    // ========================================================================
    // |================================ VENDOR ==============================|
    // ========================================================================
        Route::group(
            ['prefix' => 'vendor'],
            function() {
                Route::get('/', [VendorController::class, 'index']);
                Route::get('/{vendor}', [VendorController::class, 'show']);
                Route::post('/', [VendorController::class, 'create']);
                Route::patch('{vendor}/', [VendorController::class, 'update']);
                Route::get('/{vendor}/change-status', [VendorController::class, 'changeStatus']);
        });
    
    // ========================================================================
    // |=============================== CUSTOMER =============================|
    // ========================================================================
        Route::group(
            ['prefix' => 'customer'],
            function() {
                Route::get('/', [CustomerController::class, 'index']);
                Route::get('/{customer}', [CustomerController::class, 'show']);
                Route::post('/', [CustomerController::class, 'create']);
                Route::patch('/{customer}', [CustomerController::class, 'update']);
                Route::get('/{customer}/change-status', [CustomerController::class, 'changeStatus']);
        });

    // ========================================================================
    // |=============================== BRANCH ===============================|
    // ========================================================================
        Route::group(
            ['prefix' => 'branch'],
            function() {
                Route::get('/', [BranchCompanyController::class, 'index']);
                Route::get('/{branch}', [BranchCompanyController::class, 'show']);
                Route::post('/', [BranchCompanyController::class, 'create']);
                Route::patch('/{branch}/update', [BranchCompanyController::class, 'update']);
                Route::get('/{branch}/change-status', [BranchCompanyController::class, 'changeStatus']);
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
    
        Route::prefix('unit')->group(function() {
            Route::get('/', [UnitController::class, 'index']);
            Route::post('/', [UnitController::class, 'store']);
            Route::post('/{unit}', [UnitController::class, 'show']);
            Route::patch('/{unit}', [UnitController::class, 'update']);
            Route::get('/{unit}/change-status', [UnitController::class, 'changeStatus']);
        });
    
        Route::prefix('category')->group(function() {
            Route::get('/', [CategoryProductController::class, 'index']);
            Route::post('/', [CategoryProductController::class, 'store']);
            Route::post('/{category}', [CategoryProductController::class, 'show']);
            Route::patch('/{category}', [CategoryProductController::class, 'update']);
            Route::get('/{category}/change-status', [CategoryProductController::class, 'changeStatus']);
        });
        
    });
});
