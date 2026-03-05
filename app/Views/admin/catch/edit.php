<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>

<div class="container mt-4">

    <h3>漁獲量登録</h3>

    <form action="/admin/catch/save" method="post">

        <div class="form-group mt-3">
            <label>作成日付</label>
            <input type="date" name="create_date" class="form-control"
                   value="<?= date('Y-m-d') ?>" required>
        </div>

        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>魚種</th>
                    <th>漁獲量（kg）</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>トラフグ</td>
                    <td><input type="number" name="torafugu" class="form-control" required></td>
                </tr>
                <tr>
                    <td>マダイ</td>
                    <td><input type="number" name="madai" class="form-control" required></td>
                </tr>
                <tr>
                    <td>マハタ</td>
                    <td><input type="number" name="mahata" class="form-control" required></td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary mt-3">登録</button>
        <a href="admin/catch" class="btn btn-secondary mt-3">戻る</a>

    </form>

</div>

<?= $this->endSection() ?>
