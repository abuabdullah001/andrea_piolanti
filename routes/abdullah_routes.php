<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\backend\admin\BannerController;


Route::prefix('admin')->name('admin.')->group(function () {

    // banner
    Route::prefix('banner')->name('banner.')->group(function () {


    Route::get('/index', [BannerController::class, 'index'])->name('index');
    Route::get('/create', [BannerController::class, 'create'])->name('create');
    Route::post('/store', [BannerController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [BannerController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [BannerController::class, 'update'])->name('update');
    Route::post('/delete/{id}', [BannerController::class, 'destroy'])->name('delete');

    Route::post('/status/{id}', [BannerController::class, 'updateStatus'])->name('status');


});






});
