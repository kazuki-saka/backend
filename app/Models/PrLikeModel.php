<?php

namespace App\Models;

use CodeIgniter\Model;

//PR動画テーブル
class PrLikeModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_t_prlikes';

    //主キー
    protected $primaryKey = 'FishKind';

    //戻り値型
    protected $returnType = 'array';

    //タイムスタンプ設定
    protected $useTimestamps = false;

    //更新フィールド
    protected $allowedFields = [
        'FishKind',
        'LikeCnt'
    ];


    // ++++++++++ メソッド ++++++++++

}