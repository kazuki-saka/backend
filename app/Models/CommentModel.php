<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

//コメントテーブル
class CommentModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_t_comments';


    // ++++++++++ メソッド ++++++++++

    //利用者認証トークンからコメント情報取得
    public function GetData($iToken)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT id FROM cmsb_t_comments WHERE token = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $iToken,
        );
        
        $cnt = 0;
        foreach ($result->getResult() as $row){
            $data['id'][$cnt] = $row->id;
            $cnt = $cnt + 1;
        }

        $data['cnt'] = $cnt;

        return $data;
    }
}