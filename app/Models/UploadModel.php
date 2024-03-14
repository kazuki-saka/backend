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
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "INSERT INTO cmsb_uploads (createdTime, tableName, fieldName, recordNum, filePath, urlPath, width, height, thumbWidth, thumbHeight, info1)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            date("Y-m-d H:i:s"),
            "t_report",
            "imgbefore",
            $iId,
            $iImgPath,
            $iImgPath,
            100,        //幅
            100,        //高さ
            100,        //thumb幅
            100,        //thumb高さ
            "生産者投稿画像1"
        );

        return 200;
    }
}