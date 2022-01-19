<?php

use App\Models\User;
use App\Models\Product;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategotyController;

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('users')->group(function () {
        Route::get('', [UserController::class, 'index']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::put('{user}', [UserController::class, 'update']);
        Route::delete('{user}', [UserController::class, 'destroy']);
        Route::get('sales', [UserController::class, 'sales']);
    });
    Route::prefix('categories')->group(function () {
        Route::get('', [CategotyController::class, 'index']);
    });
    Route::prefix('products')->group(function () {
        Route::get('{category?}', [ProductController::class, 'index']);
        Route::post('', [ProductController::class, 'store']);
        Route::get('show/{product}', [ProductController::class, 'show']);
        Route::post('{product}', [ProductController::class, 'update']);
        Route::delete('{product}', [ProductController::class, 'destroy']);
        Route::get('d/{product}', [ProductController::class, 'downloadProduct']);
    });
});

Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('payment/pay',[PaymentController::class,'pay'])->name('payment.pay');
});

Route::post('payment/verify',[PaymentController::class,'verify'])->name('verify');


Route::get('test',function (){
//    return UserResource::collection(\App\Models\User::latest('id')->paginate());
//    return \App\Models\User::find(1)->products()->get()->contains(\App\Models\Product::find(1));
    User::find(2)->products()->detach();
});


