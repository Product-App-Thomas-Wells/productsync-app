<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/sources/search',  'App\Http\Controllers\SourceController@api_search')->name('source.api_search');
Route::get('/products/search',  'App\Http\Controllers\ProductController@api_search')->name('product.api_search');
Route::post('/products',  'App\Http\Controllers\ProductController@store')->name('product.store');
Route::get('/sources/getMappingRecords',  'App\Http\Controllers\SourceController@getMappingRecords')->name('source.getMappingRecords');
Route::get('/products/getMappingRecords',  'App\Http\Controllers\ProductController@getMappingRecords')->name('product.getMappingRecords');
Route::post('/sources/MappingValues',  'App\Http\Controllers\SourceController@saveMappingValues')->name('source.saveMappingValues');
Route::post('/products/MappingValues',  'App\Http\Controllers\ProductController@saveMappingValues')->name('product.saveMappingValues');