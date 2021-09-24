<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\MainController;
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
    Route::get('/', function () { return redirect('/main'); });

    // set routes for apps
    Route::get('/main', [MainController::class, 'index']);

    // new, edit, clone labels
    Route::post('/labels/store', [MainController::class, 'store_labels']);
    Route::post('/labels/delete', [MainController::class, 'delete_labels']);

    // new, edit, clone cards
    Route::post('/cards/store', [MainController::class, 'store_cards']);
    Route::post('/cards/delete', [MainController::class, 'delete_cards']);

    // new, edit, clone cards
    Route::post('/examples/store', [MainController::class, 'store_examples']);
    Route::post('/examples/delete', [MainController::class, 'delete_examples']);

    // set routes for quiz
    Route::get('/quiz/done/{id}', [QuizController::class, 'setDone']);
    Route::get('/quiz/reset', [QuizController::class, 'reset']);


    // FIXME: To move below routes to routes/api.php 
    // you need to enable Passport to support authentication


    // set routes for datatable
    Route::post('/datatable', [MainController::class, 'datatable']);

    // set routes for autocomplete
    Route::get('/autocomplete', [MainController::class, 'autocomplete']);
});



