<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SSOController;
use App\Http\Controllers\HtmlToPdfController;
use App\Http\Controllers\WordToPdfController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::any('/login', [SSOController::class, 'login']);

Route::any('/doLogin', [SSOController::class, 'doLogin']);

Route::any('/verifyTmpTicket', [SSOController::class, 'verifyTmpTicket']);

Route::any('/logout', [SSOController::class, 'logout']);

// word转pdf
Route::any('/convert_word_to_pdf', [WordToPdfController::class, 'convertWordToPdf']);

// HTML转pdf
Route::any('/convert_html_to_pdf', [HtmlToPdfController::class, 'convertToPdf']);

Route::any('/convert_html_to_pdf_customer', [HtmlToPdfController::class, 'convertToPdfCustomer']);

//Route::any('/convert_html_to_pdf', function () {
//    phpinfo();
//});

// zip打包
Route::any('/convert_zip', [HtmlToPdfController::class, 'convertToZip']);

// PDF文件操作
Route::any('/convert_add_word_to_pdf', [\App\Http\Controllers\PDFController::class, 'add']);


// 打印机
Route::any('/printer_list', [\App\Http\Controllers\PrinterController::class, 'list']);

Route::any('/printer_queue', [\App\Http\Controllers\PrinterController::class, 'queue']);

Route::any('/printer_print', [\App\Http\Controllers\PrinterController::class, 'print']);

