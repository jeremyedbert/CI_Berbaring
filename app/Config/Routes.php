<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
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
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Course::landing');
$routes->get('/course', 'Course::index', ['filter' => 'auth']);
$routes->get('/course/(:segment)/info', 'Course::info/$1', ['filter' => 'auth']);
$routes->get('/course/(:segment)/edit', 'Course::edit/$1', ['filter' => 'auth']);
$routes->add('/course/(:segment)/update', 'Course::update/$1', ['filter' => 'auth']);
$routes->get('/course/(:segment)/publish', 'Course::publish/$1', ['filter' => 'auth']);
$routes->get('/course/(:segment)/detail', 'Subchapter::index/$1', ['filter' => 'auth']);
$routes->add('/course/(:segment)/detail/add', 'Subchapter::add/$1', ['filter' => 'auth']);
$routes->get('/course/(:segment)/detail/(:segment)/edit', 'Subchapter::edit/$1/$2', ['filter' => 'auth']);
$routes->add('/course/(:segment)/detail/(:segment)/update', 'Subchapter::update/$1/$2', ['filter' => 'auth']);
$routes->get('/course/(:segment)/detail/(:segment)/delete', 'Subchapter::delete/$1/$2', ['filter' => 'auth']);
$routes->get('/course/(:segment)', 'Course::course_page/$1');
$routes->get('/course/(:segment)/learn/(:any)', 'Course::course_subchapter_page/$1/$2', ['filter' => 'auth']);
$routes->get('/course/(:segment)/enroll', 'StudentActivity::enroll/$1', ['filter' => 'auth']);
$routes->get('/course/(:segment)/start', 'StudentActivity::startCourse/$1', ['filter' => 'auth']);
$routes->add('/course/add', 'Course::add', ['filter' => 'auth']); //method post buat tambah course baru
$routes->get('/search', 'Course::search');
$routes->add('/login/process', 'User::login');
$routes->add('/register/process', 'User::register');
$routes->get('/logout', 'User::logout');
$routes->add('/homepage/pelajar/(:any)', 'StudentActivity::page/$1', ['filter' => 'auth']);
$routes->add('/homepage/mentor', 'MentorActivity::page', ['filter' => 'auth']);

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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
