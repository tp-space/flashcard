<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ExampleController;

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

Route::get('/', function () { return redirect('/cards'); });

Route::resource('cards', CardController::class);
Route::resource('labels', LabelController::class);
Route::resource('examples', ExampleController::class);

Route::get('/quiz', function () { return view('quiz'); });

