<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\backend\admin\BannerController;
use App\Http\Controllers\Web\backend\admin\MissionController;
use App\Http\Controllers\Web\backend\admin\NewsController;
use App\Http\Controllers\Web\backend\admin\NewsLetterController;
use App\Http\Controllers\Web\backend\ContactController;

Route::prefix('admin')->name('admin.')->group(function () {

    // banner
    Route::prefix('banner')->name('banner.')->group(function () {


    Route::get('/index', [BannerController::class, 'index'])->name('index');
    Route::get('/create', [BannerController::class, 'create'])->name('create');
    Route::post('/store', [BannerController::class, 'store'])->name('store');
    Route::get('/edit/{id}', [BannerController::class, 'edit'])->name('edit');
    Route::put('/update/{id}', [BannerController::class, 'update'])->name('update');
    Route::post('/delete/{id}', [BannerController::class, 'destroy'])->name('delete');
    });


    // news
    Route::prefix('news')->name('news.')->group(function () {

        Route::get('/index', [NewsController::class, 'index'])->name('index');
        Route::get('/create', [NewsController::class, 'create'])->name('create');
        Route::post('/store', [NewsController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [NewsController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [NewsController::class, 'update'])->name('update');
        Route::post('/delete/{id}', [NewsController::class, 'destroy'])->name('delete');
        Route::get('/show/{id}', [NewsController::class, 'show'])->name('show');

        Route::post('/toggle-status/{id}', [NewsController::class, 'toggleStatus'])
        ->name('toggleStatus');

    });


    // contact
    Route::prefix('contact')->name('contact.')->group(function () {
        Route::get('/index', [ContactController::class, 'index'])->name('index');
        Route::post('/delete/{id}', [ContactController::class, 'destroy'])->name('delete');
    });


    // mission
    Route::prefix('mission')->name('mission.')->group(function () {
        Route::get('/index', [MissionController::class, 'index'])->name('index');
        Route::get('/create', [MissionController::class, 'create'])->name('create');
        Route::post('/store', [MissionController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MissionController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MissionController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MissionController::class, 'destroy'])->name('delete');

        Route::post('/toggle-status/{id}', [MissionController::class, 'toggleStatus'])
        ->name('toggleStatus');
    });

    // newsletter
    Route::prefix('newsletter')->name('newsletter.')->group(function () {
        Route::get('/index', [NewsLetterController::class, 'index'])->name('index');
        Route::get('/create', [NewsLetterController::class, 'create'])->name('create');
        Route::post('/store', [NewsLetterController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [NewsLetterController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [NewsLetterController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [NewsLetterController::class, 'destroy'])->name('delete');
    });

});

