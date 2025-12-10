<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function($routes) {
    
    // 1. Autentikasi
    $routes->post('register', 'Api\Auth::register');
    $routes->post('login', 'Api\Auth::login');

    // 2. Manajemen Event (CRUD)
    $routes->resource('events', ['controller' => 'Api\Events']);
    
    // 3. Manajemen Master Tamu (CRUD)
    $routes->resource('guests', ['controller' => 'Api\Guests']);
    
    // 4. Manajemen Kategori Tamu (Opsional)
    $routes->resource('categories', ['controller' => 'Api\Categories']);

    // 5. Transaksi Tiket (Pendaftaran Peserta)
    $routes->post('tickets', 'Api\Ticket::create'); // Bikin Tiket Baru
    $routes->get('events/(:num)/participants', 'Api\Ticket::listByEvent/$1'); // Lihat Peserta per Event

    // 6. SCANNER (Fitur Utama Check-in)
    $routes->post('checkin', 'Api\Scanner::validate');
});