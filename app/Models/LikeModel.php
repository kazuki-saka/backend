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
    protected $table = 't_like';

    //暗号化キー
    protected $key;

    // ++++++++++ メソッド ++++++++++

    //利用者認証トークンからほしいね情報取得
    public function GetData($iToken)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT * FROM t_like WHERE token = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $iToken,
        );
        
        $cnt = 0;
        $data['result'] = 0;
        foreach ($result->getResult() as $row){
            $data[$cnt]['id'] = $row->id;
            $data[$cnt]['update_time'] = $row->update_time;
            $data['result'] = 1;
            $cnt = $cnt + 1;
        }

        return $data;
    }
}