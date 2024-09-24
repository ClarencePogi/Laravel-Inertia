<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FeaturesController;

Route::get('/', function () {
    return Inertia::render('App');
});

Route::get('/features', [FeaturesController::class, 'index']);

Route::get('/about', function() {
    sleep(1);
    return Inertia::render('About');
});

Route::post('/logout', function() {
   dd(request('name'));
});
