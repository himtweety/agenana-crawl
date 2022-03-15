<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
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

Route::get('/aa', function () {
    return view('welcome');
});


Route::get('/', [SiteController::class, 'create'])->name('site.create');
Route::post('/', [SiteController::class, 'store'])->name('site.store');
Route::get('/site/{site}', [SiteController::class, 'show'])->name('site.result');
Route::get('/site-status/{site}', [SiteController::class, 'showPagesCrawlStatus'])->name('site.status');


Route::get('site-data/{site}', [PageController::class, 'get_data'])->name('site.getdata');
