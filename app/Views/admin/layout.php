<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理画面</title>

    <script>
    $(document).ready(function() {
        $('.control-sidebar').remove();
        $('.control-sidebar-btn').remove();
    });
    </script>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="/public/adminlte/assets/css/adminlte.min.css">

    <!-- Bootstrap (AdminLTE 同梱) -->
    <link rel="stylesheet" href="/public/adminlte/assets/plugins/bootstrap/css/bootstrap.min.css">

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="/adminlte/assets/css/adminlte.min.css">
    <link rel="stylesheet" href="/adminlte/assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="/public/css/admin.css">

    <!-- 必要なプラグイン（jQuery / Bootstrap） -->
    <script src="/adminlte/assets/plugins/jquery/jquery.min.js"></script>
    <script src="/adminlte/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE JS -->
    <script src="/adminlte/assets/js/adminlte.js"></script>

</head>

<body class="layout-fixed sidebar-mini">

<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <span class="navbar-brand ml-2">管理画面</span>
    </nav>

    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="/public/admin/" class="brand-link">
            <span class="brand-text font-weight-light">漁獲量管理</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">

                    <li class="nav-item">
                        <a href="/public/admin/catch/register" class="nav-link">
                            <i class="nav-icon fas fa-fish"></i>
                            <p>漁獲量の登録</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/public/admin/catch/csv" class="nav-link">
                            <i class="nav-icon fas fa-file-csv"></i>
                            <p>履歴CSVの出力</p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="/public/admin/logout" class="nav-link">
                            <i class="nav-icon fas fa-sign-out-alt"></i>
                            <p>ログアウト</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- メインコンテンツ -->
    <div class="content-wrapper p-3">

        <?php $message = session()->getFlashdata('message'); ?>
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
                <?= $message ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php $error = session()->getFlashdata('error'); ?>
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?= $this->renderSection('content') ?> 
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