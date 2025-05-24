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

Route::prefix('surattugas')->group(function () {
    Route::prefix('perjadin')->group(function () {
        Route::get('/', 'SuratTugasController@index')->name('surattugas.index');
        Route::get('/create', 'SuratTugasController@create')->name('surattugas.create');
        Route::post('/store', 'SuratTugasController@store')->name('surattugas.store');
        Route::get('/edit/{access_token}', 'SuratTugasController@edit')->name('surattugas.edit');
        Route::put('/update/{access_token}', 'SuratTugasController@update')->name('surattugas.update');
        Route::post('/laporan/upload/{access_token}', 'SuratTugasController@upload')->name('surattugas.upload');
        Route::get('/print/surattugas/{access_token}', 'SuratTugasController@printSuratTugas')->name('surattugas.print');
    });
    Route::prefix('rekap-perjadin')->group(function () {
        Route::get('/', 'RekapPerjadinController@index')->name('rekap.index');
        Route::get('/export/pdf', 'RekapPerjadinController@exportPdf')->name('rekap.exportPdf');
        Route::get('/export/excel', 'RekapPerjadinController@exportExcel')->name('rekap.exportExcel');
    });

});

Route::get('/tes', function(){
    return view('surattugas::surattugas.tes');
});
