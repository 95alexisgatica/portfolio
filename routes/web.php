<?php

use Illuminate\Support\Facades\Route;



Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['es', 'en', 'pt'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::get('/', function () {
    return view('portfolio');
});
