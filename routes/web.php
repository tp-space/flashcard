<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\FilterController;

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

// Set Homepage
Route::get('/', function () { return redirect('/cards'); });

// Set routes for Cards, Labels and Examples
Route::resource('cards', CardController::class);
Route::resource('labels', LabelController::class);
Route::resource('examples', ExampleController::class);

// Set routes for filtering
Route::get('/filter/{source}/{id}/{target}', [FilterController::class, 'setSingleFilter']);
Route::post('/filter', [FilterController::class, 'setAllFilters']);

// set route for quiz
Route::get('/quiz', function () { return view('quiz'); });

