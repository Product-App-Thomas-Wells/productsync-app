<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScriptController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return view('welcome');
	return redirect('/products');
})->middleware(['verify.shopify'])->name('home');

Route::resource('products', App\Http\Controllers\ProductController::class)->middleware(['verify.shopify']);
Route::resource('sources', App\Http\Controllers\SourceController::class)->middleware(['verify.shopify']);
//Route::resource('products', App\Http\Controllers\ProductController::class);
//Route::resource('sources', App\Http\Controllers\SourceController::class);
Route::get('/scripts/pull_products/trilanco',  [ScriptController::class, 'pull_products_trilanco']);

//Auth::routes();

//Route::get('/home', [App\Http\Controllers\ProductController::class, 'index'])->middleware(['verify.shopify'])->name('home2');
Route::get('/home', [App\Http\Controllers\ProductController::class, 'index'])->name('home2');
