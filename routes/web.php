<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$app->group(['middleware' => 'cors'], function($app) {
$app->get('/', function () use ($app) {
    return $app->version();
});

$app->post('/auth/fblogin', 'AuthController@fblogin');
$app->post('/auth/login', 'AuthController@postLogin');
$app->post('/auth/register', 'AuthController@register');
$app->post('/auth/refresh', 'AuthController@refresh');

$app->group(['prefix' => 'api/v1', 'middleware' => ['auth:api', 'jwt.auth']], function($app) {

    // Logged In User Route Group
    $app->group(['prefix' => 'me'], function ($app) {
        $app->get('/', 'UserController@me');
        $app->get('/users', 'UserController@users');
        $app->post('/update', 'UserController@updateInfo');
        $app->get('/requests', 'UserController@pendingRequests');
        $app->get('/check/{id}', 'UserController@checkRequest');
        $app->get('/friends', 'UserController@friends');
        $app->post('/approve/{id}', 'UserController@approve');
        $app->post('/unfriend/{id}', 'UserController@remove');
        $app->post('/add/{id}', 'UserController@add');

        $app->get('/messages', 'ChatController@myMessages');
    });

    //Other Users Route Group
    $app->group(['prefix' => 'user'], function($app) {
        $app->get('/{id}', 'UserController@getUser');
        $app->post('/{id}/message', 'ChatController@message');
    });

    // Activity Route Group
    $app->group(['prefix' => 'activity'], function($app) {
        $app->get('/', 'ActivityController@index');
        $app->get('/{id}', 'ActivityController@show');
        $app->post('/', 'ActivityController@store');
        $app->patch('/{id}', 'ActivityController@update');
        $app->delete('/{id}', 'ActivityController@delete');
        $app->post('/{id}/tag', 'ActivityController@tag');
        $app->post('/{id}/untag', 'ActivityController@untag');
    });

    $app->group(['prefix' => 'meeting'], function ($app) {
        $app->get('/', 'MeetingController@index');
        $app->post('/', 'MeetingController@store');
        $app->get('/{id}', 'MeetingController@show');
        $app->patch('/{id}', 'MeetingController@update');
        $app->delete('/{id}', 'MeetingController@delete');
    });
    
    
    // Modules Route Group
    $app->group(['prefix' => 'module'], function($app) {
        $app->get('/', 'ModuleController@index');
        $app->post('/', 'ModuleController@store');
        $app->get('/{id}', 'ModuleController@show');
        $app->patch('/{id}', 'ModuleController@update');
        $app->delete('/{id}', 'ModuleController@delete');
        $app->post('/{id}/tag', 'ModuleController@tag');
        $app->post('/{id}/untag', 'ModuleController@untag');
    });

    // Submodules Route Group
    $app->group(['prefix' => 'submodule'], function($app) {
        $app->get('/', 'SubmoduleController@index');
        $app->post('/', 'SubmoduleController@store');
        $app->get('/{id}', 'SubmoduleController@show');
        $app->patch('/{id}', 'SubmoduleController@update');
        $app->delete('/{id}', 'SubmoduleController@delete');
    });

    $app->group(['prefix' => 'personaltask'], function($app) {
        $app->get('/', 'PersonalActivityController@index');
        $app->post('/', 'PersonalActivityController@store');
        $app->get('/{id}', 'PersonalActivityController@show');
        $app->patch('/{id}', 'PersonalActivityController@update');
        $app->delete('/{id}', 'PersonalActivityController@delete');
    });
});
});