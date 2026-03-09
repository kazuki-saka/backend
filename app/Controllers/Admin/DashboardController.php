<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CatchModel;

//管理画面のダッシュボード画面への制御
class DashboardController extends BaseController
{
    //初期表示
    public function index()
    {
        log_message('debug', 'DashboardController start');
        $model = new CatchModel();
        $latest = $model->getLatest();
        $latestDate = $latest ? $latest['CreateDate'] : null;
        $recent5 = $model->getRecentDays($latestDate, 5);

        return view('admin/dashboard', [
            'latest' => $latest,
            'recent5' => $recent5
        ]);
    }

    //登録画面の呼び出し
    public function register()
    {
        return view('admin/catch/register');
    }

    //最新日付の取得
    public function getDataByDate()
    {
        log_message('debug', 'DashboardController getDataByDate');
        $date = $this->request->getGet('date');

        $model = new CatchModel();
        $data = $model->getByDate($date);

        return $this->response->setJSON($data);
    }

    //漁獲量のデータ保存
    public function save()
    {
        log_message('debug', 'DashboardController save');
        $model = new CatchModel();

        $createDate = $this->request->getPost('create_date');

        $data = [
            'CreateDate' => $createDate,
            'Torafugu'   => $this->request->getPost('torafugu'),
            'Madai'      => $this->request->getPost('madai'),
            'Mahata'     => $this->request->getPost('mahata'),
            'UpdateDate' => date('Y-m-d H:i:s')
        ];

        // 既存データ確認
        $exists = $model->getByDate($createDate);

        if ($exists) {
            // 更新
            $model->update($createDate, $data);
        } else {
            // 新規
            $model->insert($data);
        }

        return redirect()->to('admin');
    }

    //漁獲量のデータをCSVで出力
    public function csv()
    {
        $model = new CatchModel();
        $rows = $model->orderBy('CreateDate', 'DESC')->findAll();

        $filename = 'catch_history_' . date('Ymd') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $fp = fopen('php://output', 'w');

        fputcsv($fp, ['日付', 'トラフグ', 'マダイ', 'マハタ', '更新日時']);

        foreach ($rows as $r) {
            fputcsv($fp, [
                $r['CreateDate'],
                $r['Torafugu'],
                $r['Madai'],
                $r['Mahata'],
                $r['UpdateDate']
            ]);
        }

        fclose($fp);
        exit;
    }
}