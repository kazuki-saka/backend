<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

//アップロードテーブル
class UploadModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_uploads';

    //暗号化キー
    protected $key;


    // ++++++++++ メソッド ++++++++++

    //アップロードデータへの登録
    public function AddData(string $iId, string $iImgPath)
    {

    }
}