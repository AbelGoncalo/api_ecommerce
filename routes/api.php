<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    ProductController,
    LocationController,
    CategoryController,
    BrandController,
    OrderController
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware'=>'api','prefix'=>'auth'], function ($router){
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/register', [AuthController::class,'register']);
    Route::post('/logout',[AuthController::class, 'logout']);
    Route::post('/refresh',[AuthController::class, 'refresh']);
    Route::get('/user-profile',[AuthController::class,'userProfile']);
});

//BRAND CRUD
Route::group(['prefix'=>'brand'], function($routes){
    Route::controller(BrandController::class)->group(function(){
        Route::get('/index','index')->middleware('is_admin');
        Route::get('/show/{brand_id}','show')->middleware('is_admin');
        Route::post('/store','store')->middleware('is_admin');
        Route::put('/updateBrand','updateBrand')->middleware('is_admin');
        Route::delete('deleteBrand/{brand_id}','deleteBrand')->middleware('is_admin');
    });
});

//CATEGORY CRUD
Route::group(['prefix'=>'category'], function($router){
    Route::controller(CategoryController::class)->group(function(){
        Route::get('/index','index');
        Route::get('/show/{brand_id}','show');
        Route::post('/store','store');
        Route::put('/updateCategory','updateCategory');
        Route::delete('deleteCategory/{brand_id}','deleteCategory');
    });
});

//LOCATION CRUD
Route::group(['prefix'=>'location'], function($router){
    Route::controller(LocationController::class)->group(function(){
        Route::post('/store','store');
        Route::put('/update/{location_id}','update');
        Route::delete('/destroy/{location_id}','destroy');
    });
});

//LOCATION CRUD
Route::group(['prefix'=>'product'],function($router){
    Route::controller(ProductController::class)->group(function(){
        Route::get('/','index');
        Route::get('/{product_id}','show');
        Route::post('/','store');
        Route::put('/{product_id}','update');
        Route::delete('/{product_id}','destroy');
    });
});

//ORDERS CRUD
Route::group(['prefix'=>'order'],function(){
    Route::controller(OrderController::class)->group(function(){
        Route::get('/index','index');
        Route::get('/show/{order_id}','show');
        Route::post('store','store');
        Route::get('/get_order_items/{order_id}','get_order_items')->middleware('is_admin');
        Route::get('/get_user_orders/{user_id}','get_user_orders')->middleware('is_admin');
        Route::post('/change_order_status/{order_id}','change_order_status')->middleware('is_admin');
    });
});
