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

use Illuminate\Support\Facades\Route;

Route::prefix('surattugas')->group(function() {
     Route::prefix('dinas-luar')->group(function () {
         Route::get('/', 'PerjalananDinasController@index')->name('perjadin.index');
         Route::get('/create', 'PerjalananDinasController@create')->name('perjadin.create');
         Route::post('/store', 'PerjalananDinasController@store')->name('perjadin.store');
         Route::get('/edit/{access_token}', 'PerjalananDinasController@edit')->name('perjadin.edit');
         Route::put('/update/{access_token}', 'PerjalananDinasController@update')->name('perjadin.update');
         Route::post('/laporan/upload/{access_token}', 'PerjalananDinasController@upload')->name('perjadin.upload');
         Route::get('/printPerjadin/{access_token}', 'PerjalananDinasController@printPerjadin')->name('perjadin.print');
     });
});
