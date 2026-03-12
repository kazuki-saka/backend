<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CatchModel;

//管理画面の履歴CSVの制御
class CatchController extends BaseController
{

    public function csvForm()
    {
        return view('admin/catch/csvform');
    }

    //漁獲量ダウンロード
    public function csvDownload()
    {
        $start = $this->request->getPost('start_date');
        $end   = $this->request->getPost('end_date');

        if (!$start || !$end) {
            return redirect()->back()->with('error', '開始日と終了日を指定してください。');
        }

        $model = new CatchModel();
        $data = $model->GetRangeDays($start, $end);
                    
        // CSV生成
        $filename = "漁獲量_{$start}_{$end}.csv";

        // Excel で文字化けしないよう Shift-JIS で出力
        header('Content-Type: text/csv; charset=Shift_JIS');
        header("Content-Disposition: attachment; filename={$filename}");

        $fp = fopen('php://output', 'w');

        // ヘッダ行
        $header = ['日付', '若狭ふぐ', '敦賀真鯛', '若狭まはた'];
        mb_convert_variables('SJIS-win', 'UTF-8', $header);
        fputcsv($fp, $header);

        // データ行
        foreach ($data as $row) {
            // UTF-8 → SJIS に変換
            mb_convert_variables('SJIS-win', 'UTF-8', $row);

            fputcsv($fp, [
                $row['CreateDate'],
                $row['Torafugu'],
                $row['Madai'],
                $row['Mahata']
            ]);
        }

        fclose($fp);
        exit;
    }

}