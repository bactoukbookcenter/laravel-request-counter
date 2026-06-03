<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/count', function () {
    $count = (int) Cache::get('request_count', 0);

    return request()->wantsJson()
        ? response()->json(['count' => $count])
        : view('counter', ['count' => $count]);
});

Route::post('/reset', function () {
    Cache::put('request_count', 0);
    return redirect('/count');
});

Route::post('/api/reset', function () {
    Cache::put('request_count', 0);
    return response()->json(['ok' => true]);
});
