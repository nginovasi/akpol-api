<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('mobile', ['namespace' => 'App\Modules\Mobile\Controllers', 'filter' => 'api-auth'], function($subroutes){
    $subroutes->add('apiDev/(:any)','MobileApiDev::$1');
    $subroutes->add('iot/(:any)','MobileIot::$1');
    $subroutes->add('V1/(:any)','MobileApiV1::$1');
    $subroutes->add('V2/(:any)','MobileApiV2::$1');
    $subroutes->add('auth/(:any)','MobileAuth::$1');
    $subroutes->add('faceLinkDev/(:any)','MobileFaceDev::$1');
    $subroutes->add('publicapi/(:any)','MobilePublic::$1');
    $subroutes->add('', 'Mobile::index');
	$subroutes->add('(:any)', 'Mobile::$1');

});