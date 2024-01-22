<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);

$routes->get('/', 'Home::index');
$routes->get('ApiController', 'ApiController::Test');
$routes->get('api/event/list', 'ApiController::EventListJson');
$routes->match(['get', 'post'], 'UserTempController/Add', 'UserTempController::AddMail');
$routes->match(['get', 'post'], 'UserRegistController', 'UserRegistController::ChkToken');
$routes->match(['get', 'post'], 'UserRegistController/Add', 'UserRegistController::Regist');
$routes->match(['get', 'post'], 'SignInController', 'SignInController::ChkSignIn');
$routes->get('(:any)', 'Pages::view/$1');
