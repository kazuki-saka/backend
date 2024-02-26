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
            $sql = "SELECT t_rep.id, t_rep.title, t_rep.detail_modify, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname, t_rep.updatedDate
                FROM cmsb_t_report as t_rep
                LEFT JOIN cmsb_m_user AS m_usr ON m_usr.token = t_rep.token
                WHERE t_rep.id = ? AND t_rep.DeployFlg = 1";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iId
        );

        foreach ($result->getResult() as $row){
            //array_push($data, $row);
            $data = $row;
            break;
        }
        //$data = $result->getResult();
        return $data;         
    }

    //最新記事IDの取得
    public function GetNewId($iKind)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT id FROM cmsb_t_report WHERE fishkind = ? ORDER BY updatedDate DESC LIMIT 1";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $iKind
        );
        
        $id = "";
        switch ($iKind)
        {
            case 1:
                $id = "A";
                break;    
            case 2:
                $id = "B";
                break;    
            case 3:
                $id = "C";
                break;    
            case 4:
                $id = "D";
                break;    
        }

        $num = "00000";
        foreach ($result->getResult() as $row){
            $temp = (integer)substr($row->id, 1) + 1;
            $num = str_pad($temp, 5, 0, STR_PAD_LEFT);
        }

        $id .= $num;

        return $id;
    }

    //生産者による記事の投稿
    public function Rejist($iId, $iKind, $iTitle, $iDetail, $iToken)
    {
         // クエリ生成
         $query = $this->db->prepare(static function ($db) 
         {
            $sql = "INSERT INTO cmsb_t_report (title, id, token, fishkind, reportkbn, DeployFlg, detail)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            return (new Query($db))->setQuery($sql);
         });
     
         // クエリ実行
        $result = $query->execute(
            $iTitle,
            $iId,
            $iToken,
            $iKind,
            1,
            0,
            $iDetail
        );

        if (isset($result)){
            return 200;
        }
        else{
            return 401;
        }
    }
}