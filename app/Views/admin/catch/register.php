<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>

<div class="container mt-4">

    <h3>漁獲量登録</h3>

    <form action="/public/admin/catch/update" method="post">

        <!-- 日付入力欄（幅200px） -->
        <div class="form-group mt-3">
            <label>作成日付</label>
            <input type="date"
                   id="create_date"
                   name="create_date"
                   class="form-control input-date"
                   value="<?= date('Y-m-d') ?>"
                   required>
        </div>

        <!-- 魚種テーブル（入力欄を120pxに統一） -->
        <table class="table table-bordered mt-3" style="width: 400px;">
            <thead>
                <tr>
                    <th style="width: 180px;">魚種</th>
                    <th style="width: 180px;">漁獲量（kg）</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>若狭ふぐ</td>
                    <td>
                        <input type="number"
                               id="torafugu"
                               name="torafugu"
                               class="form-control input-weight"
                               required>
                    </td>
                </tr>
                <tr>
                    <td>敦賀真鯛</td>
                    <td>
                        <input type="number"
                               id="madai"
                               name="madai"
                               class="form-control input-weight"
                               required>
                    </td>
                </tr>
                <tr>
                    <td>若狭まはた</td>
                    <td>
                        <input type="number"
                               id="mahata"
                               name="mahata"
                               class="form-control input-weight"
                               required>
                    </td>
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

    fetch('/public/admin/catch/getDataByDate?date=' + date)
        .then(response => response.json())
        .then(data => {

            if (data && (data.Torafugu !== '' || data.Madai !== '' || data.Mahata !== '')) {
                document.getElementById('torafugu').value = data.Torafugu;
                document.getElementById('madai').value = data.Madai;
                document.getElementById('mahata').value = data.Mahata;
            } else {
                document.getElementById('torafugu').value = '';
                document.getElementById('madai').value = '';
                document.getElementById('mahata').value = '';
            }
        });
});
</script>

<?= $this->endSection() ?>
