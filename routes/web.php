<?php

use Illuminate\Support\Facades\Storage;

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

// Route::get('/', function () {
//     return view('welcome');
// });
// Auth::routes();

Route::post('import', 'DataImportController@import')->name('import');
Route::post('bd_import', 'DataImportController@importBusDropOff')->name('bd_import');
Route::get('export', 'DataImportController@export')->name('export');

//Route::post('dtd_import', 'DataImportController@importDoorToDoor')->name('dtd_import');

Route::get('letsgo', 'Auth\LoginController@showLoginForm');
Route::post('letsgo', 'Auth\LoginController@login')->name('login');
Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/letsgo_home', 'HomeController@index')->name('home');
Route::get('/send_noti', 'HomeController@sendNoti')->name('send_noti');
