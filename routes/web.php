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

// Test Route
Route::get('/test', 'TestController@test');

// Login

//Route::get('/login', 'Auth/LoginController@login');

Route::group(['prefix' => 'live', 'namespace' => 'Live'], function () {
    Route::get('/get-user/{user}', 'LiveMonitoringController@getUser');
    Route::get('/get-server', 'LiveMonitoringController@getServer');
    Route::get('show-channels', 'LiveMonitoringController@getLiveCalls')->name('live.calls');
    Route::post('listen', 'LiveMonitoringController@listenThisCall')->name('listen.call');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'can:admin-access'], function () {

    Route::get('/', 'IndexController@index')->name('admin.index');

    // User level routes

    Route::resource('user', 'UserController');
    Route::get('admin_index', 'UserController@indexAdminUser')->name('user.admin.index');
    Route::post('admin_user', 'UserController@createAdminUser')->name('user.admin');
    Route::delete('/admin_destroy/{user}', 'UserController@destroyAdminUser')->name('admin.destroy');
    Route::get('reporting_user', 'UserController@reportingIndex')->name('user.reporting');
    Route::get('validateAgentId', 'UserController@validateAgentId')->name('validate_agent');

    // Settings

    Route::get('setting', 'SettingController@index')->name('setting.index');
    Route::patch('setting', 'SettingController@update')->name('setting.update');

    // Live monitoring
    Route::view('monitoring', 'admin.live.monitoring')->name('live.monitoring');

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
    Route::get('/CallbacklistNumber', 'IndexController@getCallbackListNumber')->name('agent.callbacklist');
    Route::get('/getRecentCalls', 'IndexController@getRecentCalls')->name('agent.recent');
    Route::post('/scheduleCall', 'IndexController@scheduleCall')->name('agent.schedule');
    Route::get('/getScheduledCall', 'IndexController@getScheduledCallsTable')->name('agent.get-calls');
    Route::post('/saveResponse', 'IndexController@saveResponse')->name('agent.saveResponse');
    Route::post('/changelistNumberStatus', 'IndexController@changelistNumberStatus')->name('agent.changelistNumberStatus');
    Route::post('/changelistNumberAttempts', 'IndexController@changelistNumberAttempts')->name('agent.changelistNumberAttempts');
    Route::post('/changecallBackNumberStatus', 'IndexController@changecallBackNumberStatus')->name('agent.changecallBackNumberStatus');

    // Agent Routes
    Route::post('/status', 'AgentStatusController@getAgentStatus')->name('agent.status');
    Route::post('/ready', 'AgentStatusController@readyAgent')->name('agent.ready');
    Route::post('/notready', 'AgentStatusController@notReadyAgent')->name('agent.notready');
});

Route::group(['middleware' => 'can:access-both', 'prefix' => 'reports', 'namespace' => 'Report'], function () {
    Route::get('/', 'IndexController@index')->name('report.index');

    Route::group(['prefix' => 'login'], function () {
        Route::view('/', 'report.login.index')->name('login.index');
        Route::get('/getReport', 'LoginReportController@getReport')->name('login.report');
    });

    Route::group(['prefix' => 'code'], function () {
        Route::get('/', 'ResponseCodeReportController@index')->name('code.index');
        Route::post('/getReport', 'ResponseCodeReportController@getReport')->name('code.report');
    });
});

Route::group(['prefix' => 'reports', 'namespace' => 'Report'], function () {
    // Cdr
    Route::group(['prefix' => 'cdr'], function () {
        Route::view('/', 'report.cdr.index')->name('cdr.index');
        Route::get('/getReport', 'CdrController@getReport')->name('cdr.report');
        Route::get('/playRecording/{file}', 'CdrController@playFile')->name('cdr.play');
    });
});
