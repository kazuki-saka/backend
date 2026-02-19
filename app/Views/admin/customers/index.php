<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<?php
//ソート用のアイコン
function sort_icon($column, $currentSort, $currentOrder) {
    if ($column !== $currentSort) {
        return ''; // ソート対象外は何も表示しない
    }

    if ($currentOrder === 'asc') {
        return '<i class="fas fa-sort-up ms-1"></i>';
    } else {
        return '<i class="fas fa-sort-down ms-1"></i>';
    }
}
?>

<?php
//現在のソート状態を取得（コントローラから渡す）
$currentSort  = $sort ?? '';
$currentOrder = $order ?? '';
?>


<h1 class="mb-4">顧客一覧</h1>


<div class="card">    
    <div class="card-body">
        <form method="get">
            <input type="text" name="customer_number" placeholder="顧客番号" value="<?= esc($_GET['customer_number'] ?? '') ?>">
            <input type="text" name="customer_name" placeholder="顧客名" value="<?= esc($_GET['customer_name'] ?? '') ?>">
            <button type="submit">検索</button>
        </form>

        <div class="d-flex justify-content-end mb-3">
            <a href="/admin/customers/create" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> 新規顧客追加
            </a>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="<?= $currentSort === 'CustomerNo' ? 'table-active' : '' ?>">
                        <a href="?sort=CustomerNo&order=<?= ($currentSort === 'CustomerNo' && $currentOrder === 'asc') ? 'desc' : 'asc' ?>"
                            class="d-flex justify-content-between align-items-center">
                            <span>顧客番号</span>
                            <span><?= sort_icon('CustomerNo', $currentSort, $currentOrder); ?></span>
                        </a>
                    </th>

                    <th class="<?= $currentSort === 'CutomerName' ? 'table-active' : '' ?>">
                        <a href="?sort=CutomerName&order=<?= ($currentSort === 'CutomerName' && $currentOrder === 'asc') ? 'desc' : 'asc' ?>"
                            class="d-flex justify-content-between align-items-center">
                            <span>顧客名</span>
                            <span><?= sort_icon('CutomerName', $currentSort, $currentOrder) ?></span>
                        </a>
                    </th>

                    <th class="<?= $currentSort === 'MailAdr' ? 'table-active' : '' ?>">
                        <a href="?sort=MailAdr&order=<?= ($currentSort === 'MailAdr' && $currentOrder === 'asc') ? 'desc' : 'asc' ?>"
                            class="d-flex justify-content-between align-items-center">
                            <span>メールアドレス</span>
                            <span><?= sort_icon('MailAdr', $currentSort, $currentOrder) ?></span>
                        </a>
                    </th>

                    <th class="<?= $currentSort === 'DownloadDate' ? 'table-active' : '' ?>">
                        <a href="?sort=DownloadDate&order=<?= ($currentSort === 'DownloadDate' && $currentOrder === 'asc') ? 'desc' : 'asc' ?>"
                            class="d-flex justify-content-between align-items-center">
                            <span>前回ダウンロード日時</span>
                            <span><?= sort_icon('DownloadDate', $currentSort, $currentOrder) ?></span>
                        </a>
                    </th>

                    <th class="<?= $currentSort === 'MailSendFlg' ? 'table-active' : '' ?>">
                        <a href="?sort=MailSendFlg&order=<?= ($currentSort === 'MailSendFlg' && $currentOrder === 'asc') ? 'desc' : 'asc' ?>"
                            class="d-flex justify-content-between align-items-center">
                            <span>送信可否</span>
                            <span><?= sort_icon('MailSendFlg', $currentSort, $currentOrder) ?></span>
                        </a>
                    </th>

                    <th>操作</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($customers as $c): ?>
                <tr>
                    <td><?= esc($c['CustomerNo']) ?></td>
                    <td><?= esc($c['CutomerName']) ?><br> <?= esc($c['CutomerName2']) ?></td>
                    <td><?= esc($c['MailAdr']) ?></td>
                    <td><?= esc($c['DownloadDate']) ?></td>

                    <td>
                        <?php if ($c['MailSendFlg'] == 1): ?>
                            <span class="badge bg-success">送信可</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">送信不可</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="/admin/customers/edit/<?= $c['CustomerNo'] ?>" class="btn btn-sm btn-primary">
                            編集
                        </a>

                        <?php if ($c['MailSendFlg'] == 1): ?>
                        <a href="/admin/customers/resend/<?= $c['CustomerNo'] ?>" class="btn btn-sm btn-warning">
                            再送
                        </a>
                        <?php endif; ?>
                        <form action="/admin/customers/delete/<?= $c['CustomerNo'] ?>" 
                            method="post" 
                            style="display:inline-block;"
                            onsubmit="return confirm('本当に削除しますか？');">
                            <button class="btn btn-sm btn-outline-danger">削除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>

    </div>
</div>

<?= $this->endSection() ?>
