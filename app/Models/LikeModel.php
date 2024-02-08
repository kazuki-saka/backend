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
        
        $cnt = 0;
        foreach ($result->getResult() as $row){
            $data['id'][$cnt] = $row->id;
            $cnt = $cnt + 1;
        }

        $data['cnt'] = $cnt;

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
                $ret = 401;
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
            $ret = 402;
        }

        return $ret;
    }
}