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

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->group(['prefix' => 'api/v1'], function($app) {
    $app->get('activity', 'ActivityController@index');
    $app->get('activity/{id}', 'ActivityController@show');
    $app->post('activity', 'ActivityController@store');
    $app->patch('activity/{id}', 'ActivityController@update');
    $app->delete('activity/{id}', 'ActivityController@delete');
    $app->post('activity/{id}/tag', 'ActivityController@tag');
    $app->post('activity/{id}/untag', 'ActivityController@untag');
    
    $app->group(['prefix' => 'module'], function($app) {
        $app->get('/', 'ModuleController@index');
        $app->post('/', 'ModuleController@store');
        $app->get('/{id}', 'ModuleController@show');
        $app->patch('/{id}', 'ModuleController@update');
        $app->delete('/{id}', 'ModuleController@delete');
    });
});