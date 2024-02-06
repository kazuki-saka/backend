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

// 仮登録取得
$routes->post("/api/signup/load.preflight", "UserTempController::LoadPreflight");
// 仮登録作成
$routes->post('/api/signup/create.preflight', 'UserTempController::AddMail');
// 仮登録認証
$routes->post('/api/signup/auth.preflight', 'UserTempController::AuthPreflight');

// 利用者作成
$routes->post('/api/signup/create.user', 'UserRegistController::Regist');
// 利用者認証
$routes->post('/api/signin/auth.user', 'SignInController::ChkSignIn');
// 利用者防護
$routes->post("/api/signin/guard.user", "SignInController::GuardUser");

// TOP表示
//$routes->get('/api/top/view', 'HomeController::View');
$routes->post('/api/top/view', 'HomeController::View');

//魚種毎の記事取得
$routes->post('/api/report/view', 'ReportListController::View');


$routes->get('(:any)', 'Pages::view/$1');
