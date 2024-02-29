<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

//トピックステーブル
class TopicsModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_t_topics';


    // ++++++++++ メソッド ++++++++++

    //更新日付が新しいものから10件取得
    public function GetData($iLimit = 10)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT fishkind,detail,updatedDate FROM cmsb_t_topics ORDER BY updatedDate DESC Limit ?";
            return (new Query($db))->setQuery($sql);
        });
        
        $result = $query->execute($iLimit);

        $data = [];
        foreach ($result->getResult() as $row){
            array_push($data, $row);
        }

        //$data = $this->findAll(10, 0)->orderBy('updatedDate DESC');
        return $data;
    }

    //該当の魚種を選定してから更新日付が新しいものから10件取得
    public function GetKindData($iKind, $iLimit = 10)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT num,fishkind,detail,updatedDate FROM cmsb_t_topics WHERE fishkind = ? ORDER BY updatedDate DESC Limit ?";
            return (new Query($db))->setQuery($sql);
        });
        
        $result = $query->execute(
            $iKind,
            $iLimit
        );

        $data = [];
        foreach ($result->getResult() as $row){
            array_push($data, $row);
        }

        return $data;
    }

    public function GetIdData($iNum)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT num as id, fishkind, title, detail as detail_modify, updatedDate FROM cmsb_t_topics WHERE num = ?";
            return (new Query($db))->setQuery($sql);
        });

        $result = $query->execute(
            $iNum
        );

        $data = [];
        foreach ($result->getResult() as $row){
            //array_push($data, $row);
            $data = $row;
            break;
        }

        return $data;
    }
}