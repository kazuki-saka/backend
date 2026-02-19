<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>管理画面ログイン</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="/public/adminlte/assets/css/adminlte.css">

    <!-- jQuery / Bootstrap -->
    <script src="/public/adminlte/assets/plugins/jquery/jquery.min.js"></script>
    <script src="/public/adminlte/assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE JS -->
    <script src="/public/adminlte/assets/js/adminlte.js"></script>
</head>

<body class="login-page bg-light">

<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <h3 class="card-title">管理画面ログイン</h3>
        </div>

        <div class="card-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="/public/admin/login" method="post">
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="パスワード" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block">ログイン</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>