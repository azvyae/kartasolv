<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(true);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->get('/sandbox', 'Sandbox\Home::index');
$routes->get('/sandbox/(:any)', 'Sandbox\Home::index/$1');
$routes->get('/sejarah', 'Home::history');
$routes->get('/hubungi-kami', 'Home::contactUs');
$routes->get('/masuk', 'Auth::login');
$routes->get('/keluar', 'Auth::logout');
$routes->get('/lupa-kata-sandi', 'Auth::forgetPassword');
$routes->get('/atur-ulang-kata-sandi', 'Auth::resetPassword');

$routes->group('konten', static function ($routes) {
    $routes->get('sejarah', 'Content\History::index');
    $routes->get('profil-karang-taruna', 'Content\OrganizationProfile::index');
    $routes->group('profil-karang-taruna', static function ($routes) {
        $routes->get('info-utama', 'Content\OrganizationProfile::mainInfo');
        $routes->get('kegiatan-kami', 'Content\OrganizationProfile::ourActivities');
        $routes->get('pengurus', 'Content\OrganizationProfile::members');
        $routes->get('pengurus/(:any)', 'Content\OrganizationProfile::memberCrud/$1');
        $routes->get('tambah-pengurus', 'Content\OrganizationProfile::memberCrud');
    });
});

$routes->get('dasbor', 'User\Home::index');
$routes->get('profil', 'User\Profile::index');
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
