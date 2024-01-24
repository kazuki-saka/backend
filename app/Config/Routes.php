<?php

use CodeIgniter\Router\RouteCollection;
/**
 * ========== Remix用メソッド規約 ============
 * GETメソッド  => 取得
 * POSTメソッド => 取得 or 新規登録
 * PUTメソッド  => 更新登録
 * DELETEメソッド => 削除
 * ==========================================
 */

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');
$routes->get('ApiController', 'ApiController::Test');
$routes->get('api/event/list', 'ApiController::EventListJson');

// 仮登録作成
$routes->post('/api/signup/create.preflight', 'UserTempController::AddMail');
// 仮登録認証
$routes->post('/api/signup/auth.preflight', 'UserRegistController::ChkToken');

// 利用者作成
$routes->post('/api/signup/create.user', 'UserRegistController::Regist');
// 利用者認証
$routes->post('/api/signin/auth.user', 'SignInController::ChkSignIn');

$routes->match(['get', 'post'], 'HomeController', 'HomeController::View');
$routes->get('(:any)', 'Pages::view/$1');
