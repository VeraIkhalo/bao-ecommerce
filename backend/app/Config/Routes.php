<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->post('register', 'AuthController::register');
$routes->post('login', 'AuthController::login');
$routes->get('products', 'ProductController::index');
$routes->post('products', 'ProductController::create');
$routes->post('orders', 'OrderController::create');