<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <h3>漁獲量管理</h3>

    <div class="card mt-3">
        <div class="card-header">
            最新の登録内容
        </div>
        <div class="card-body">

            <?php if ($latest): ?>
                <p>最終更新日：<?= esc($latest['UpdateDate']) ?></p>

                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>トラフグ</th>
                            <th>マダイ</th>
                            <th>マハタ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= esc($latest['CreateDate']) ?></td>
                            <td><?= esc($latest['Torafugu']) ?> kg</td>
                            <td><?= esc($latest['Madai']) ?> kg</td>
                            <td><?= esc($latest['Mahata']) ?> kg</td>
                        </tr>
                    </tbody>
                </table>

            <?php else: ?>
                <p>まだ登録がありません。</p>
            <?php endif; ?>

        </div>
    </div>

    <div class="mt-4">
        <a href="admin/catch/register" class="btn btn-primary">登録画面へ</a>
        <a href="/public/admin/catch/csv" class="btn btn-success">履歴CSV出力</a>
        <a href="/logout" class="btn btn-danger">ログアウト</a>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            直近5日間の漁獲量
        </div>
        <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>日付</th>
                        <th>トラフグ</th>
                        <th>マダイ</th>
                        <th>マハタ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent5 as $row): ?>
                    <tr>
                        <td><?= esc($row['CreateDate']) ?></td>
                        <td><?= esc($row['Torafugu']) ?> kg</td>
                        <td><?= esc($row['Madai']) ?> kg</td>
                        <td><?= esc($row['Mahata']) ?> kg</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
