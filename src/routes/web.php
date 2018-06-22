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
use Illuminate\Http\Response;


$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->group(['middleware' => 'auth'], function () use ($router) {
   
    /**
     * This is the state routes
     */
    $router->post('state/create', 'StateController@store');
    $router->get('states', 'StateController@index');
    $router->get('state/{id}', 'StateController@show');
    $router->patch('state/edit/{id}', 'StateController@update');
    $router->delete('state/delete/{id}', 'StateController@delete');
    $router->delete('state/restore/{id}', 'StateController@restore'); 

    /**
     * This is the transaction-in route
     */
    $router->post('transaction/in/create', 'TransactionInController@store');
    $router->get('transaction/in', 'TransactionInController@index');
    $router->get('transaction/in/{id}', 'TransactionInController@show');
    $router->patch('transaction/in/edit/{id}', 'TransactionInController@update');
    $router->delete('transaction/in/delete/{id}', 'TransactionInController@delete');
    $router->delete('transaction/in/restore/{id}', 'TransactionInController@restore');

    /**
     * This is the transaction-out route
     */
    $router->get('transaction/out', 'TransactionOutController@index');
    $router->get('transaction/out/{id}', 'TransactionOutController@show');
    $router->post('transaction/out/create', 'TransactionOutController@store');
    $router->patch('transaction/out/edit/{id}', 'TransactionOutController@update');
    $router->delete('transaction/out/delete/{id}', 'TransactionOutController@delete');
    $router->delete('transaction/out/restore/{id}', 'TransactionOutController@restore');

});

$router->get('api/swagger', function () {

    // $response = new Response();
    // $response->setContent(file_get_contents(base_path("swagger.json")));
    // $response->withHeaders(['Content-Type' => 'application/json']);

    $file = file_get_contents(base_path('swagger.json'));

    return response($file)
            ->header('Content-Type', 'application/json');;

    
});

$router->post('transaction/in/payment', 'TransactionInController@createMolliePayment');
