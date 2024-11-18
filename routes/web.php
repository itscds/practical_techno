<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubcategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('layouts.app');
})->name('home');


Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/fetch-categories', [CategoryController::class, 'fetchData'])->name('categories.ajaxData');
Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

Route::get('/subcategories', [SubcategoryController::class, 'index'])->name('subcategories.index');
Route::get('fetch-subcategories', [SubcategoryController::class, 'fetchSubcategories']);
Route::get('fetch-categories', [SubcategoryController::class, 'fetchCategories']);
Route::post('/subcategories', [SubcategoryController::class, 'store']);
Route::get('/subcategories/{id}', [SubcategoryController::class, 'show']);
Route::put('/subcategories/{id}', [SubcategoryController::class, 'update']);
Route::delete('/subcategories/{id}', [SubcategoryController::class, 'destroy']);

Route::get('/fetch-subcategories/{category}', [ProductController::class, 'fetchSubcategories']);
Route::resource('products', ProductController::class);
Route::delete('/products/delete-image/{imageId}', [ProductController::class, 'deleteImage'])->name('products.deleteImage');
Route::delete('/products/delete-size/{sizeId}', [ProductController::class, 'deleteSize'])->name('products.deleteSize');