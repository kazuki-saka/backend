<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <h3>漁獲量登録</h3>

    <form action="/public/admin/catch/update" method="post">

        <div class="form-group mt-3">
            <label>作成日付</label>
            <input type="date" id="create_date" name="create_date" class="form-control"
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
                    <td><input type="number" id="torafugu" name="torafugu" class="form-control" required></td>
                </tr>
                <tr>
                    <td>マダイ</td>
                    <td><input type="number" id="madai" name="madai" class="form-control" required></td>
                </tr>
                <tr>
                    <td>マハタ</td>
                    <td><input type="number" id="mahata" name="mahata" class="form-control" required></td>
                </tr>
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary mt-3">登録</button>
        <a href="../" class="btn btn-secondary mt-3">戻る</a>

    </form>

</div>

<script>
document.getElementById('create_date').addEventListener('change', function() {

    let date = this.value;

    fetch('admin/catch/getDataByDate?date=' + date)
        .then(response => response.json())
        .then(data => {

            if (data) {
                // 既存データ → フォームに反映
                document.getElementById('torafugu').value = data.Torafugu;
                document.getElementById('madai').value = data.Madai;
                document.getElementById('mahata').value = data.Mahata;
            } else {
                // 新規 → 空欄に戻す
                document.getElementById('torafugu').value = '';
                document.getElementById('madai').value = '';
                document.getElementById('mahata').value = '';
            }
        });
});
</script>

<?= $this->endSection() ?>
