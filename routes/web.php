<?php

use Illuminate\Support\Facades\Route;

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

// Route::resource('customer', 'CustomerController');
// Route::resource('item', 'ItemController');

Route::get('/item',[
    'uses' => 'ItemController@index',
    'as' => 'item.index'
]);

Route::get('/signup', [
        'uses' => 'userController@getSignup',
        'as' => 'user.signup']);

 Route::post('/signup', [
        'uses' => 'userController@postSignup',
        'as' => 'user.signup']);

Route::get('profile', [
        'uses' => 'userController@getProfile',
        'as' => 'user.profile']);

 Route::get('logout', [
        'uses' => 'userController@getLogout',
        'as' => 'user.logout']);

  Route::get('signin', [
                'uses' => 'userController@getSignin',
                'as' => 'user.signin']);

  Route::post('/signin', [
        'uses' => 'userController@postSignin',
        'as' => 'user.signin']);
