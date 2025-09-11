<?php
use Illuminate\Support\Facades\Route;
use Markgersalia\LaravelEasyFiles\Http\Controllers\TempUploadController;

Route::post('api/easy-files/temp-upload', [TempUploadController::class, 'store'])
    ->name('api.easy-files.temp-upload');