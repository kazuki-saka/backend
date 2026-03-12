<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<div class="container mt-4">
    <h3>履歴CSV出力</h3>
    <div class="card-body">

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php
        $today = date('Y-m-d');
        $start30 = date('Y-m-d', strtotime('-30 days'));
        ?>

        <form action="csv/download" method="post">

            <div class="form-group">
                <label>開始日</label>
                <input style="width: 180px;" type="date" name="start_date" class="form-control" value="<?= $start30 ?>" required>
            </div>

            <div class="form-group mt-3">
                <label>終了日</label>
                <input style="width: 180px;" type="date" name="end_date" class="form-control" value="<?= $today ?>" required>
            </div>

            <button type="submit" class="btn btn-primary mt-4">
                CSVダウンロード
            </button>

        </form>

    </div>
</div>

<?= $this->endSection() ?>