<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('main', ['namespace' => 'App\Modules\Main\Controllers', 'filter' => 'api-auth'], function($subroutes){
    $subroutes->add('', 'Main::index');
	$subroutes->add('(:any)', 'Main::$1');
    $subroutes->add('action/(:any)','Action::$1');
    $subroutes->add('ajax/(:any)','Ajax::$1');

});