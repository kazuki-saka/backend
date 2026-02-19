<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

//漁獲量テーブル
class CatchModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_t_catch';

    protected $returnType = 'array';

    
    // ++++++++++ メソッド ++++++++++

    public function GetData()
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT t_catch.CreateDate, m_FishKind.FishName, t_catch.Volume, t_catch.UpdateDate FROM cmsb_t_catch AS t_catch
                    LEFT JOIN cmsb_m_FishKind AS m_FishKind ON t_catch.FishType = m_FishKind.FishKind ORDER BY t_catch.updatedDate DESC";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute();

        $data =[];
        foreach ($result->getResult() as $row){
            array_push($data, $row);
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
                    LEFT JOIN cmsb_m_user AS m_user ON t_comment.token = m_user.token WHERE t_comment.id = ? ORDER BY t_comment.updatedDate";
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