<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <h3 class="mb-4">新規顧客追加</h3>

    <form action="/admin/customers/store" method="post">

        <div class="mb-3">
            <label class="form-label">顧客番号</label>
            <input type="text" name="CustomerNo" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">顧客名</label>
            <input type="text" name="CutomerName" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">顧客名２</label>
            <input type="text" name="CutomerName2" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">メール送信フラグ</label>
            <br>
            <select name="MailSendFlg" class="form-select">
                <option value="1">送信可</option>
                <option value="0">送信不可</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">メールアドレス</label>
            <input type="email" name="MailAdr" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">サブメール1</label>
            <input type="email" name="MailAdrSub1" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">サブメール2</label>
            <input type="email" name="MailAdrSub2" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">サブメール3</label>
            <input type="email" name="MailAdrSub3" class="form-control">
        </div>

        <button class="btn btn-primary">登録</button>
        <a href="/admin/customers" class="btn btn-secondary">戻る</a>

    </form>

</div>

<?= $this->endSection() ?>
