<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

//漁獲量テーブル
class CatchModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_t_catchs';

    //主キー
    protected $primaryKey = 'CreateDate';

    //戻り値型
    protected $returnType = 'array';

    //タイムスタンプ設定
    protected $useTimestamps = false;

    //更新フィールド
    protected $allowedFields = [
        'CreateDate',
        'Torafugu',
        'Madai',
        'Mahata',
        'UpdateDate'
    ];


    // ++++++++++ メソッド ++++++++++

    // 最新日付を取得
    public function getLatest()
    {
        return $this->orderBy('CreateDate', 'DESC')
                    ->first();
    }

    // 今日のデータ取得
    public function getByDate($date)
    {
        return $this->where('CreateDate', $date)->first();
    }

    public function getDataByDate()
    {
        $date = $this->request->getGet('date');

        $model = new CatchModel();
        $data = $model->getByDate($date);

        return $this->response->setJSON($data);
    }

    //直近5日のデータ取得
    public function getRecentDays($latestDate, $limit = 5)
    {
        return $this->where('CreateDate !=', $latestDate)
                    ->orderBy('CreateDate', 'DESC')
                    ->limit($limit)
                    ->find();
    }

}