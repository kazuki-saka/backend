<?php

namespace App\Models;

use CodeIgniter\Model;

//PR動画テーブル
class PrModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_m_pr';


    // ++++++++++ メソッド ++++++++++

    //公開フラグが１になっているデータを全て取得
    public function GetData()
    {
        return $this->where(['DeployFlg' => 1])->findAll();
    }
}