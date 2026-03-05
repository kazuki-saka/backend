<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理画面</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="/public/adminlte/assets/css/adminlte.min.css">

    <!-- Bootstrap (AdminLTE 同梱) -->
    <link rel="stylesheet" href="/public/adminlte/assets/plugins/bootstrap/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="/public/adminlte/assets/plugins/fontawesome-free/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body class="hold-transition">

<div class="wrapper">

    <!-- ヘッダー -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">

        <!-- 右側メニュー -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="/logout" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i> ログアウト
                </a>
            </li>
        </ul>
    </nav>

    <!-- メインコンテンツ -->
    <div class="content-wrapper" style="margin-left:0 !important;">

        <section class="content pt-4">
            <div class="container-fluid">
                <?= $this->renderSection('content') ?>
            </div>
        </section>

    </div>

</div>

<!-- jQuery -->
<script src="/public/adminlte/assets/plugins/jquery/jquery.min.js"></script>

<!-- Bootstrap -->
<script src="/public/adminlte/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- AdminLTE -->
<script src="/public/adminlte/assets/js/adminlte.min.js"></script>

</body>
</html>