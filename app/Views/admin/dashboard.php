<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="container-fluid">

    <h1 class="mb-4">管理画面トップ</h1>

    <div class="row">

        <!-- 顧客数カード（任意） -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?= $customerCount ?? '-' ?></h3>
                    <p>登録顧客数</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="/admin/customers" class="small-box-footer">
                    顧客一覧へ <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- 通知メール再送ページへの導線（任意） -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>-</h3>
                    <p>通知メール管理</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <a href="/admin/notification" class="small-box-footer">
                    通知メール一覧へ <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>
    <!-- 最新の通知メール送信履歴 -->
    <div class="card mt-4">
        <div class="card-header">
            <strong>最新の通知メール送信履歴</strong>
        </div>

        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>送信日時</th>
                        <th>顧客番号</th>
                        <th>通知種別</th>
                        <th>ステータス</th>
                        <th>詳細</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (empty($latestNotifications)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">データがありません</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($latestNotifications as $log): ?>
                        <tr>
                            <td><?= esc($log['sent_at']) ?></td>
                            <td><?= esc($log['CustomerNo']) ?></td>
                            <td>
                                <?= ($log['notification_type'] === 'invoice') ? '請求書通知' : 'お知らせ通知' ?>
                            </td>
                            <td>
                                <?php if ($log['status'] === 'success'): ?>
                                    <span class="badge bg-success">成功</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">失敗</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="/admin/notification/<?= $log['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    詳細
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

        <div class="card-footer text-end">
            <a href="/admin/notification" class="btn btn-sm btn-secondary">もっと見る</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>