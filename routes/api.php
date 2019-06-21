<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


route::get('list_aduan/{page}/{dataPerpage}','api\AduanController@index')->name("listAduan");
route::post('store_aduan','api\AduanController@store')->name("storeAduan");
route::post('register','api\UserController@register')->name("register");
route::post('login','api\UserController@login')->name("login");
route::get("detail_aduan/{id}",'api\AduanController@detailAduan');
route::get("my_aduan/{id}/{page}/{dataPerpage}","api\AduanController@myAduan");
route::post("update_img","api\UserController@updateImage");

Route::group(['middleware' => ['jwt.auth']], function() {
	route::post('ubah_status_aduan','api\AduanController@ubahStatus');

});
