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

// TOP表示（トピックスの取得）
$routes->post('/api/top/view', 'HomeController::View');

//漁獲量の取得
$routes->get('/api/top/catch.latest', 'HomeController::latest');

$routes->post('/api/report/view', 'ReportListController::View');
//魚種毎の記事取得（市場関係者）
$routes->post('/api/report/market.viewlist', 'ReportListController::GetMarketReport');

//魚種毎の記事取得（生産者）
$routes->post('/api/report/fishman.viewlist', 'ReportListController::GetFishmanReport');




//記事詳細取得
//$routes->get('/api/detail/view', 'ReportDetailController::View');
$routes->post('/api/detail/view', 'ReportDetailController::View');

//ほしいね更新
$routes->post('/api/detail/likeup', 'ReportDetailController::likeup');

//コメント追加
$routes->post('/api/detail/Comment', 'ReportDetailController::RejistComment');

//記事の投稿
$routes->post('/api/report/add', 'ReportDetailController::RejistReport');

//記事の画像投稿
$routes->post('/api/report/addimg', 'ReportDetailController::RejistReportImg');

//問い合わせ時の初期表示
$routes->post('/api/inquiry/view', 'InquiryController::View');

//問い合わせ時のメール送信
$routes->post('/api/inquiry/send', 'InquiryController::Send');

//自分がしたほしいね一覧取得
$routes->post('/api/mernu/likelist', 'MenuController::GetLikeList');

//自分が投稿した一覧取得
$routes->post('/api/mernu/Rejistlist', 'MenuController::GetRejistList');

//PR動画のいいね
$routes->get('api/like/get', 'PrLikeController::get');
$routes->post('api/like/add', 'PrLikeController::add');

//管理者用ページ
$routes->get('admin/login', 'Admin\LoginController::index');
$routes->post('admin/login', 'Admin\LoginController::auth');
$routes->get('admin/logout', 'Admin\LoginController::logout');

$routes->group('admin', ['filter' => 'adminAuth'], function($routes) {
$routes->get('/', 'Admin\DashboardController::index');
$routes->get('customers', 'Admin\CustomerController::index');
});

$routes->group('admin', ['filter' => 'adminAuth'], function($routes) {
    $routes->get('customers', 'Admin\CustomerController::index');
});

//管理者ページ　登録画面
$routes->get('admin/catch/register', 'Admin\DashboardController::register');
$routes->post('admin/catch/update', 'Admin\DashboardController::save');

//管理者ページ　CSVダウンロード
$routes->get('admin/catch/csv', 'Admin\CatchController::csvForm');
$routes->post('admin/catch/csv/download', 'Admin\CatchController::csvDownload');

$routes->get('admin/catch/getDataByDate', 'Admin\DashboardController::getDataByDate');


$routes->post('/api/test', 'UserTempController::test');

$routes->get('(:any)', 'Pages::view/$1');
