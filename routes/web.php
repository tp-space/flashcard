<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Auth;

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

// Injects routes for login, logout, register, ....
Auth::routes();

Route::middleware('auth')->group(function () {

// Set Homepage
Route::get('/', function () { return redirect('/labels'); });

// Set routes for Cards, Labels and Examples
Route::resource('cards', CardController::class);
Route::resource('labels', LabelController::class);
Route::resource('examples', ExampleController::class);

// Set routes for filtering
Route::get('/filter/{source}/{id}/{target}', [FilterController::class, 'setSingleFilter']);
Route::post('/filter', [FilterController::class, 'setAllFilters']);

// set routes for quiz
Route::get('/quiz', [QuizController::class, 'index']);
Route::get('/quiz/done/{id}', [QuizController::class, 'setDone']);
Route::get('/quiz/reset', [QuizController::class, 'reset']);

// set routes for quiz state
Route::post('/quiz/update_state', [QuizController::class, 'updateState']);

});


