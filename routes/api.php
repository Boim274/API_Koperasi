<?php

use App\Http\Controllers\Api\AddToCartController;
use App\Http\Controllers\Api\Admin\KategoriController;
use App\Http\Controllers\Api\Admin\ProdukController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['middleware' => 'auth:sanctum'], function () {

    // logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Grup Auth Sanctrum Admin
Route::group(['middleware' => ['auth:sanctum',AdminMiddleware::class],'prefix' =>'admin'], function () {
    // Categories
    Route::get('/kategoris', [KategoriController::class, 'index']);
    Route::post('/kategoris', [KategoriController::class, 'store']);
    Route::get('/kategoris/{id}', [KategoriController::class, 'show']);
    Route::post('/kategoris/{id}', [KategoriController::class, 'update']);
    Route::delete('/kategoris/{id}', [KategoriController::class, 'destroy']);

    // produks
    Route::get('/produks', [ProdukController::class, 'index']);
    Route::post('/produks', [ProdukController::class, 'store']);
    Route::get('/produks/{id}', [ProdukController::class, 'show']);
    Route::post('/produks/{id}', [ProdukController::class, 'update']);
    Route::delete('/produks/{id}', [ProdukController::class, 'destroy']);
});   

Route::group(['middleware' => ['auth:sanctum','isPelanggan']], function () {
    Route::get('/add-to-carts', [AddToCartController::class, 'index']);
    Route::get('/add-to-carts/{id}', [AddToCartController::class, 'show']);
    Route::post('/add-to-carts', [AddToCartController::class, 'store']);
    Route::patch('/add-to-carts/{id}', [AddToCartController::class, 'update']);
    Route::delete('/add-to-carts/{id}', [AddToCartController::class, 'destroy']);

    Route::post('/checkout', [CheckoutController::class, 'store']);       
    Route::get('/checkout', [CheckoutController::class, 'index']);       
    Route::delete('/checkout/{id}', [CheckoutController::class, 'destroy']);
});



// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

