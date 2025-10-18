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

// Role-specific dashboards (Task 3)
// Protect role routes with RoleAuth (Task 4)
$routes->group('admin', ['filter' => 'roleauth'], static function($routes) {
    $routes->get('dashboard', 'Admin::dashboard');
});
$routes->group('teacher', ['filter' => 'roleauth'], static function($routes) {
    $routes->get('dashboard', 'Teacher::dashboard');
});

// Course enrollment routes
$routes->post('/course/enroll', 'Course::enroll');

// Announcements routes (Midterm)
$routes->get('/announcements', 'Announcement::index');