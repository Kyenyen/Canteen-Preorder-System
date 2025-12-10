<?php

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

// Catch all for Vue SPA (exclude HTML files in public folder)
Route::get('/{any}', function () {
    return view('canteen.index');
})->where('any', '^(?!.*\.html$).*$');