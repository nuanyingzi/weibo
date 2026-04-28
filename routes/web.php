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


Route::get('/', 'StaticPagesController@home')->name('home');
Route::get('/help', 'StaticPagesController@help')->name('help');
Route::get('/about', 'StaticPagesController@about')->name('about');

Route::get('signup', 'UsersController@create')->name('signup');
Route::get('signup/confirm/{token}', 'UsersController@confirmEmail')->name('confirm_email'); // 邮箱验证

// 用户资源路由
Route::resource('users', 'UsersController');

// 登录-注销路由
Route::get('login', 'SessionsController@create')->name('login');
Route::post('login', 'SessionsController@store')->name('login');
Route::delete('logout', 'SessionsController@destroy')->name('logout');

// 密码重置
Route::get('password/reset', 'PasswordController@showLinkRequestForm')->name('password.request');
Route::post('password/email', 'PasswordController@sendResetLinkEmail')->name('password.email');

Route::get('password/reset/{token}', 'PasswordController@showResetForm')->name('password.reset');
Route::post('password/reset', 'PasswordController@reset')->name('password.update');

// 文章资源路由
Route::resource('statuses', 'StatusesController', ['only' => ['store', 'destroy']]);

// 关注资源路由
Route::get('/users/{user}/followings', 'UsersController@followings')->name('users.followings');
// 粉丝资源路由
Route::get('/users/{user}/followers', 'UsersController@followers')->name('users.followers');
// 关注-取消关注资源路由
Route::post('/users/followers/{user}', 'FollowersController@store')->name('followers.store');
// 取消关注资源路由
Route::delete('/users/followers/{user}', 'FollowersController@destroy')->name('followers.destroy');



