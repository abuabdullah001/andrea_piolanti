<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
require __DIR__.'/abdullah_routes.php';
