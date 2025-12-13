<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Group API
$routes->group('api', function ($routes) {

    // ====================================================
    // 1. RUTE PUBLIK (Bisa diakses tanpa Token)
    // ====================================================
    $routes->post('register', 'Api\Auth::register');
    $routes->post('login', 'Api\Auth::login');


    // ====================================================
    // 2. RUTE PRIVATE (Wajib Token / Login)
    // Menggunakan filter 'authFilter' yang sudah dibuat
    // ====================================================
    $routes->group('', ['filter' => 'authFilter'], function ($routes) {

        // --- Fitur User Profile ---
        $routes->get('user/profile', 'Api\User::profile');
        $routes->post('user/profile', 'Api\User::updateProfile');
        $routes->post('user/password', 'Api\User::changePassword');
        $routes->post('user/settings', 'Api\User::updateSettings');

        // --- Manajemen Event (CRUD) ---
        $routes->resource('events', ['controller' => 'Api\Events']);
        $routes->get('events/(:num)/attendance', 'Api\Reports::realtime/$1');

        // --- Manajemen Tamu (CRUD) ---
        $routes->resource('guests', ['controller' => 'Api\Guests']);

        // --- Transaksi Tiket ---
        $routes->post('tickets', 'Api\Ticket::create');
        $routes->get('events/(:num)/participants', 'Api\Ticket::listByEvent/$1');

        // --- Scanner & Check-in ---
        $routes->post('scanner/scan', 'Api\Scanner::validate');
        $routes->post('scanner/manual-checkin', 'Api\Scanner::manual');

        // --- Laporan ---
        $routes->get('reports', 'Api\Reports::index');
        $routes->get('reports/(:num)', 'Api\Reports::show/$1');
        $routes->get('reports/(:num)/logs', 'Api\Reports::logs/$1');
    });
});
