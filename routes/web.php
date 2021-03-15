<?php

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

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    //return view('welcome');
    return view('landing.favison');
});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
//Route::get('/agent1', 'AgentController@index')->name('agent');
//Route::get('/test', 'AgentController@test')->name('test');

// Login

//Route::get('/login', 'Auth/LoginController@login');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'can:admin-access'], function () {

    Route::get('/', 'IndexController@index')->name('admin.index');

    // User level routes

    Route::resource('user', 'UserController');
    Route::get('reporting_user', 'UserController@reportingIndex')->name('user.reporting');
    Route::get('validateAgentId', 'UserController@validateAgentId')->name('validate_agent');

    // Settings

    Route::get('setting', 'SettingController@index')->name('setting.index');
    Route::patch('setting', 'SettingController@update')->name('setting.update');

    // Response Codes

    Route::resource('responseCode', 'ResponseCodeController')->except(['show']);

    // List CSV

    Route::resource('list', 'UploadListController');

    // Sub List

    Route::get('list/{parent}/sublist', 'SubListController@index')->name('sublist.index');
    Route::get('list/sublist/{listNumber}/edit', 'SubListController@edit')->name('sublist.edit');
    Route::patch('list/sublist/{listNumber}', 'SubListController@update')->name('sublist.update');
    Route::delete('list/sublist/{listNumber}', 'SubListController@destroy')->name('sublist.destroy');
});

Route::group(['middleware' => 'can:agent-access', 'prefix' => 'agent', 'namespace' => 'Agent'], function () {
    Route::get('/', 'IndexController@index')->name('agent.index');
    Route::get('/randomNumber', 'IndexController@getGenNumber')->name('agent.random');
    Route::get('/listNumber', 'IndexController@getListNumber')->name('agent.list');
    Route::get('/getRecentCalls', 'IndexController@getRecentCalls')->name('agent.recent');
    Route::post('/scheduleCall', 'IndexController@scheduleCall')->name('agent.schedule');
    Route::get('/getScheduledCall', 'IndexController@getScheduledCallsTable')->name('agent.get-calls');
    Route::post('/saveResponse', 'IndexController@saveResponse')->name('agent.saveResponse');
});

Route::group(['middleware' => 'can:access-both', 'prefix' => 'reports', 'namespace' => 'Report'], function () {
    Route::get('/', 'IndexController@index')->name('report.index');

    // Cdr

    Route::group(['prefix' => 'cdr'], function () {
        Route::view('/', 'report.cdr.index')->name('cdr.index');
        Route::get('/getReport', 'CdrController@getReport')->name('cdr.report');
        Route::get('/playRecording/{file}', 'CdrController@playFile')->name('cdr.play');
        Route::get('/getAutoGenReport', 'CdrController@getAutoGenReport')->name('cdr.autogenreport');
        Route::get('/getSelfDialReport', 'CdrController@getSelfDialReport')->name('cdr.selfdialreport');
        Route::get('/playRecording/{file}', 'CdrController@playFile')->name('cdr.play');
    });

    Route::group(['prefix' => 'login'], function () {
        Route::view('/', 'report.login.index')->name('login.index');
        Route::get('/getReport', 'LoginReportController@getReport')->name('login.report');
    });

    Route::group(['prefix' => 'code'], function () {
        Route::get('/', 'ResponseCodeReportController@index')->name('code.index');
        Route::post('/getReport', 'ResponseCodeReportController@getReport')->name('code.report');
    });
});
