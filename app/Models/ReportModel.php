<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

//記事テーブル
class ReportModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_t_report';

    //暗号化キー
    protected $key;


    // ++++++++++ メソッド ++++++++++

    //指定した記事IDの情報を取得する
    public function GetData($iId)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT t_rep.num as id, t_rep.fishkind, t_rep.title, t_rep.detail_modify, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname, t_rep.updatedDate
                FROM cmsb_t_report as t_rep
                INNER JOIN cmsb_m_user AS m_usr ON m_usr.token = t_rep.token
                WHERE t_rep.num = ? AND t_rep.DeployFlg = 1";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iId
        );

        $data = [];
        foreach ($result->getResult() as $row){
            //array_push($data, $row);
            $data = $row;
            break;
        }
        //$data = $result->getResult();
        
        //改行コードを<br>に変換する
        //$data->detail_modify = nl2br($data->detail_modify, false);

        return $data;         
    }

    //生産者による記事の投稿
    public function Rejist($iKind, $iTitle, $iDetail, $iToken)
    {
         // クエリ生成
         $query = $this->db->prepare(static function ($db) 
         {
            $sql = "INSERT INTO cmsb_t_report (title, token, fishkind, reportkbn, DeployFlg, detail)
                    VALUES (?, ?, ?, ?, ?, ?)";
            return (new Query($db))->setQuery($sql);
         });
     
         // クエリ実行
        $result = $query->execute(
            $iTitle,
            $iToken,
            $iKind,
            1,
            0,
            $iDetail
        );

                  //生産者による投稿
                  //投稿時は非公開
    if (isset($result)){
            return 200;
        }
        else{
            return 401;
        }
    }
}