<?php

use App\Http\Controllers\FileUploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', ['products' => \App\Models\ProductData::all()]);
});

Route::post('/file-upload', [FileUploadController::class, 'uploadFile']);
Route::get('/delete/{productId}', [FileUploadController::class, 'deleteProduct']);
