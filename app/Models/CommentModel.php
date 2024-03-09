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

    protected $returnType = 'array';

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
        
        $data['id'] = [];
        foreach ($result->getResult() as $row){
            if (!in_array($row->id, $data['id'])){
                array_push($data['id'], $row->id);
            }
        }

        return $data;
    }

    //該当の記事IDに対するコメント一覧を取得する
    public function GetIdData($iId)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT t_comment.num, comment, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) as nickname, t_comment.updatedDate FROM cmsb_t_comments AS t_comment
                    LEFT JOIN cmsb_m_user AS m_user ON t_comment.token = m_user.token WHERE t_comment.id = ? ORDER BY t_comment.updatedDate DESC";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iId
        );
        
        $data =[];
        foreach ($result->getResult() as $row){
            array_push($data, $row);
        }

        return $data;
    }

    //該当の記事IDに対するコメントを登録する
    public function Rejist($iId, $iToken, $iComment)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "INSERT INTO cmsb_t_comments ( `title`, `id`, `token`, `comment`) 
                VALUES (?,?,?,?)";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            "*",
            $iId,
            $iToken,
            $iComment
        );
        
        //$row = $result->getRow();
        if (isset($result)){
            return 200;
        }
        else{
            return 401;
        }
    }
}