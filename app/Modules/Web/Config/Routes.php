<?php

if(!isset($routes))
{ 
    $routes = \Config\Services::routes(true);
}

$routes->group('web', ['namespace' => 'App\Modules\Web\Controllers', 'filter' => 'api-auth'], function($subroutes){
    $subroutes->add('auth/(:any)','WebAuth::$1');
    $subroutes->add('registrasitaruna/(:any)','WebRegistrasitaruna::$1');
    $subroutes->add('administrator/(:any)','WebAdministrator::$1');
    $subroutes->add('akademikkhs/(:any)','WebAkademikkhs::$1');
    $subroutes->add('panicbutton/(:any)','WebPanicbutton::$1');
    $subroutes->add('masterdata/(:any)','WebMasterdata::$1');
    $subroutes->add('penilaian/(:any)','WebPenilaian::$1');
    $subroutes->add('akademik/(:any)','WebAkademik::$1');
    $subroutes->add('schedule/(:any)','WebSchedule::$1');
    $subroutes->add('telegram/(:any)','WebTelegram::$1');
    $subroutes->add('portal/(:any)','WebPortal::$1');
    $subroutes->add('ldap/(:any)','WebLdap::$1');
    $subroutes->add('files/(:any)','WebFiles::$1');
    $subroutes->add('main/(:any)','WebMain::$1');
    $subroutes->add('', 'Web::index');
	$subroutes->add('(:any)', 'Web::$1');

});