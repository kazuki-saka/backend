<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<h1 class="mb-4">顧客編集</h1>

<div class="card">
    <div class="card-body">

        <form action="/admin/customers/update/<?= esc($customer['CustomerNo']) ?>" method="post">

            <div class="mb-3">
                <label class="form-label">顧客番号</label>
                <input type="text" class="form-control" value="<?= esc($customer['CustomerNo']) ?>" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">顧客名</label>
                <input type="text" name="CutomerName" class="form-control" 
                       value="<?= esc($customer['CutomerName']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">顧客名２</label>
                <input type="text" name="CutomerName2" class="form-control" 
                       value="<?= esc($customer['CutomerName2']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">メールアドレス</label>
                <input type="email" name="MailAdr" class="form-control" 
                       value="<?= esc($customer['MailAdr']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">メール送信可否</label>
                <select name="MailSendFlg" class="form-select">
                    <option value="1" <?= $customer['MailSendFlg'] == 1 ? 'selected' : '' ?>>送信可</option>
                    <option value="0" <?= $customer['MailSendFlg'] == 0 ? 'selected' : '' ?>>送信不可</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">サブメール1</label>
                <input type="email" name="MailAdrSub1" class="form-control" 
                       value="<?= esc($customer['MailAdrSub1']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">サブメール2</label>
                <input type="email" name="MailAdrSub2" class="form-control" 
                       value="<?= esc($customer['MailAdrSub2']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">サブメール3</label>
                <input type="email" name="MailAdrSub3" class="form-control" 
                       value="<?= esc($customer['MailAdrSub3']) ?>">
            </div>

            <button type="submit" class="btn btn-primary">保存</button>
            <a href="/admin/customers" class="btn btn-secondary">戻る</a>

        </form>

    </div>
</div>

<?= $this->endSection() ?>
