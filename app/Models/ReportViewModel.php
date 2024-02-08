<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

//記事Viewテーブル
class ReportViewModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_t_report';

    //暗号化キー
    protected $key;


    // ++++++++++ メソッド ++++++++++

    //魚種単位で記事を取得（ビューテーブルの方を参照）
    public function GetKindData($iKind)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT id, title, detail_m, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname, updatedDate FROM cmsb_v_report WHERE fishkind = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iKind
        );

        $data = [];
        foreach ($result->getResult() as $row){
            array_push($data, $row);
        }

        return $data;
    }

    //記事ID単位で記事を取得（ビューテーブルの方を参照）
    public function GetIdData($iId)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT id, title, detail_m, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname, updatedDate FROM cmsb_v_report WHERE id = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iId
        );

        $data = $result->getResult();

        return $data;

    }
}