<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Authentication routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/auth/getUser', 'Auth::getUser');

// Roles dashboard routes
$routes->get('/dashboard', 'Auth::dashboard');

// Course enrollment routes
$routes->post('/course/enroll', 'Course::enroll');

// Course search routes
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');

// Materials management routes
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');

// Materials list per course (students/admin)
$routes->get('/course/(:num)/materials', 'Materials::list/$1');

// Notifications routes
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

