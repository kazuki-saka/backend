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
            $sql = "SELECT t_rep.num as id, t_rep.fishkind, t_rep.title, t_rep.detail_modify, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname,
                        t_rep.updatedDate, upload.filePath
                FROM cmsb_t_report as t_rep
                INNER JOIN cmsb_m_user AS m_usr ON m_usr.token = t_rep.token
                LEFT JOIN cmsb_uploads AS upload ON upload.recordNum = t_rep.num AND upload.fieldName = 'imgafter'
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
            if($data->filePath){
                $data->filePath= "/report/after/" .  $data->filePath;
            }  
            break;
        }
        //$data = $result->getResult();
        
        //改行コードを<br>に変換する
        //$data->detail_modify = nl2br($data->detail_modify, false);
        //<BR>を消す
        //<br>を改行コードに変換
        $data->detail_modify = preg_replace('/<br[[:space:]]*\/?[[:space:]]*>/i', "\n", $data->detail_modify);

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
            1,          //生産者による投稿
            0,          //投稿時は非公開
            $iDetail
        );
                  
        if (isset($result)){
            //登録したIDを取得する
            // クエリ生成
            $query = $this->db->prepare(static function ($db) 
            {
                $sql = "SELECT num FROM cmsb_t_report WHERE token = ? ORDER BY updatedDate DESC";
                return (new Query($db))->setQuery($sql);
            });            

            $result = $query->execute(
                $iToken
            );

            foreach ($result->getResult() as $row){
                $id = $row->num;
                break;
            }

            return $id;
        }
        else{
            return -1;
        }
    }
}