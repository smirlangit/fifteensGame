<?php

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

Route::get('/', function(){

    return view('startgame');
})->name('home')->middleware("auth");



//API интерфейс
Route::post('/api/game', 'GameController@createNewGame')->middleware("auth");
Route::post('/api/game/{id}/solve', 'GameController@checkSolve')->middleware("auth");


Route::post('/register', 'Auth\RegisterController@create');
Route::post('/logout', 'Auth\LoginController@logout');
Auth::routes();

