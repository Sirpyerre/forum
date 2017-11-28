<?php

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('{provider}/auth', [
	'uses' => 'SocialsController@auth',
	'as' => 'social.auth'
]);

Route::get('/{provider}/redirect',[
	'uses' => 'SocialsController@auth_callback',
	'as' => 'social.callback'
]);