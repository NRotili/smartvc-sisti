<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//test-email
Route::get('/send-test-email', function () {
    try {
        \Illuminate\Support\Facades\Mail::to('rotilinicolas@gmail.com')->send(new \App\Mail\TestMail());
        return 'Test email sent!';
    } catch (\Exception $e) {
        return 'Error sending email: ' . $e->getMessage();
    }
});
