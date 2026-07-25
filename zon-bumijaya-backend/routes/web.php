<?php

use Illuminate\Support\Facades\Route;

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

\Illuminate\Support\Facades\DB::listen(function($query) {
    file_put_contents(storage_path('logs/queries.log'), $query->sql . ' [' . implode(', ', $query->bindings) . "]\n", FILE_APPEND);
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ready', function() {
    return 'OK';
});

require __DIR__.'/auth.php';