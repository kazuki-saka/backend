<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>


<div class="container-fluid">

    <h3>在庫尾数管理</h3>

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
                            <th style="width: 140px;">日付</th>
                            <th style="width: 180px;" class="text-center">若狭ふぐ</th>
                            <th style="width: 180px;" class="text-center">敦賀真鯛</th>
                            <th style="width: 180px;" class="text-center">若狭まはた</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= esc($latest['CreateDate']) ?></td>
                            <td class="text-right"><?= number_format($latest['Torafugu']) ?></td>
                            <td class="text-right"><?= number_format($latest['Madai']) ?></td>
                            <td class="text-right"><?= number_format($latest['Mahata']) ?></td>
                        </tr>
                    </tbody>
                </table>

            <?php else: ?>
                <p>まだ登録がありません。</p>
            <?php endif; ?>

        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            直近5日間の在庫尾数
        </div>
        <div class="card-body">

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th style="width: 140px;">日付</th>
                        <th style="width: 180px;" class="text-center">若狭ふぐ</th>
                        <th style="width: 180px;" class="text-center">敦賀真鯛</th>
                        <th style="width: 180px;" class="text-center">若狭まはた</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent5 as $row): ?>
                    <tr>
                        <td><?= esc($row['CreateDate']) ?></td>
                        <td class="text-right"><?= number_format($row['Torafugu']) ?></td>
                        <td class="text-right"><?= number_format($row['Madai']) ?></td>
                        <td class="text-right"><?= number_format($row['Mahata']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
