<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/subscriber/{id}', "Subscriber@read") -> middleware('auth');
Route::post('/subscriber/{id}', "Subscriber@create") -> middleware('auth');
Route::patch('/subscriber/{id}', "Subscriber@update") -> middleware('auth');
Route::delete('/subscriber/{id}', "Subscriber@delete") -> middleware('auth');

Route::get('/list/{id}', "List@read") -> middleware('auth');
Route::post('/list/{id}', "List@create") -> middleware('auth');
Route::patch('/list/{id}', "List@update") -> middleware('auth');
Route::delete('/list/{id}', "List@delete") -> middleware('auth');

Route::get('/subscriber-list/{id}', "SubscriberList@read") -> middleware('auth');
Route::post('/subscriber-list/{id}', "SubscriberList@create") -> middleware('auth');
Route::delete('/subscriber-list/{id}', "SubscriberList@delete") -> middleware('auth');
