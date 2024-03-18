<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

//ほしいねテーブル
class LikeModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_t_likes';

    //暗号化キー
    protected $key;


    // ++++++++++ メソッド ++++++++++

    //利用者認証トークンからほしいね情報取得
    public function GetData($iToken)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT id FROM cmsb_t_likes WHERE token = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $iToken
        );
        
        /*
        $data['id'] = [];
        foreach ($result->getResult() as $row){
            array_push($data['id'], $row->id);
        }
*/
        $data = [];
        foreach ($result->getResult() as $row){
            array_push($data, $row->id);
        }

        return $data;
    }

    //記事IDからほしいね情報取得
    public function GetIdData($iId)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT COUNT(id) AS num FROM cmsb_t_likes WHERE id = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $iId
        );
        
        $cnt = 0;
        foreach ($result->getResult() as $row){
            $data = $row->num;
            break;
        }

        return $data;
    }

    //該当記事IDのデータを更新する
    public function UpCount($iId, $iToken)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT id FROM cmsb_t_likes WHERE id = ? AND token = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $iId,
            $iToken
        );

        $ret = 0;
        if ($result != null){
            //既にほしいね済み
            foreach ($result->getResult() as $row){
                $ret = 200;
                return $ret;
            }
        }

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "INSERT INTO cmsb_t_likes (title, id, token)
                    VALUES (?, ?, ?)";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            "*",
            $iId,
            $iToken
        );

        if ($result != null){
            $ret = 200;
        }
        else{
            $ret = 412;
        }

        return $ret;
    }

    public function GetMyLikeReport($iToken)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT t_rep.num as reportid, t_rep.title, t_rep.fishkind, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname, t_lik.updatedDate
                FROM cmsb_t_likes as t_lik
                LEFT JOIN cmsb_t_report as t_rep ON t_rep.num = t_lik.id 
                LEFT JOIN cmsb_m_user AS m_usr ON m_usr.token = t_rep.token
                WHERE t_lik.token = ? AND t_rep.DeployFlg = 1
                ORDER BY t_lik.updatedDate DESC";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iToken
        );

        $data = [];
        foreach ($result->getResult() as $row){
            array_push($data, $row);
        }
       
        return $data;
    }
}