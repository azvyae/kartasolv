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
$routes->get('sejarah', 'Home::history');
$routes->get('hubungi-kami', 'Home::contactUs');
$routes->get('dasbor', 'User\Home::index');
$routes->get('phpinfo', 'Sandbox\Home::phpinfo');
$routes->get('verifikasi', 'Auth::verifyEmail');
$routes->delete('keluar', 'Auth::index');

$routes->group('masuk', static function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->post('/', 'Auth::index');
});

$routes->group('profil', static function ($routes) {
    $routes->get('/', 'User\Profile::index');
    $routes->put('/', 'User\Profile::index');
});

$routes->group('sandbox', static function ($routes) {
    $routes->get('/', 'Sandbox\Home::sandboxOne');
    $routes->put('/', 'Sandbox\Home::sandboxOne');
});

$routes->group('sandbox2', static function ($routes) {
    $routes->get('/', 'Sandbox\Home::sandboxTwo');
    $routes->put('/', 'Sandbox\Home::sandboxTwo');
});

$routes->group('lupa-kata-sandi', static function ($routes) {
    $routes->get('/', 'Auth::forgetPassword');
    $routes->post('/', 'Auth::forgetPassword');
});

$routes->group('atur-ulang-kata-sandi', static function ($routes) {
    $routes->get('/', 'Auth::resetPassword');
    $routes->put('/', 'Auth::resetPassword');
});

$routes->group('konten', static function ($routes) {
    $routes->group('sejarah', static function ($routes) {
        $routes->get('/', 'Content\History::index');
        $routes->put('/', 'Content\History::index');
    });
    $routes->group('profil-karang-taruna', static function ($routes) {
        $routes->get('/', 'Content\OrganizationProfile::index');

        $routes->group('info-utama', static function ($routes) {
            $routes->get('/', 'Content\OrganizationProfile::mainInfo');
            $routes->put('/', 'Content\OrganizationProfile::mainInfo');
        });
        $routes->group('kegiatan-kami', static function ($routes) {
            $routes->get('/', 'Content\OrganizationProfile::ourActivities');
            $routes->put('/', 'Content\OrganizationProfile::ourActivities');
        });
        $routes->group('pengurus', static function ($routes) {
            $routes->get('/', 'Content\OrganizationProfile::members');
            $routes->group('tambah', static function ($routes) {
                $routes->get('/', 'Content\OrganizationProfile::memberCrud');
                $routes->post('/', 'Content\OrganizationProfile::memberCrud');
            });
            $routes->group('(:alphanum)', static function ($routes) {
                $routes->get('/', 'Content\OrganizationProfile::memberCrud/$1');
                $routes->put('/', 'Content\OrganizationProfile::memberCrud/$1');
                $routes->delete('/', 'Content\OrganizationProfile::memberCrud/$1');
            });
        });
    });
});


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
