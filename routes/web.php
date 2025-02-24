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

Route::prefix('/admin')->namespace('App\Http\Controllers\Admin')->group(function(){
    Route::match(['get','post'],'login','AdminController@login');
    Route::group(['middleware' => ['admin']], function(){
        Route::get('dashboard', 'AdminController@dashboard');
        Route::match(['get', 'post'],'update-password', 'AdminController@updatePassword');
        Route::match(['get', 'post'],'update-details', 'AdminController@updateDetails');
        Route::post('check-current-password', 'AdminController@checkCurrentPassword');
        Route::get('logout', 'AdminController@logout');

        //Display CMS Pages (CRUD - READ)
        Route::get('cms-pages', 'CmsController@index');
        Route::post('update-cms-page-status', 'CmsController@update');
        Route::match(['get','post'],'add-edit-cms-page/{id?}', 'CmsController@edit');
        Route::get('delete-cms-page/{id?}', 'CmsController@destroy');

        //Subadmins
        Route::get('subadmins', 'AdminController@subadmins');
        Route::post('update-subadmin-status', 'AdminController@updateSubadminStatus');
        Route::match(['get','post'],'add-edit-subadmin/{id?}', 'AdminController@addEditSubAdmin');
        Route::get('delete-subadmin/{id?}', 'AdminController@deleteSubadmin');
        Route::match(['get','post'],'update-role/{id?}', 'AdminController@updateRole');
        

    });
    
    
});


