<?php

namespace App\Models;

use CodeIgniter\Model;

class ListViewModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'v_report';

    // ++++++++++ メソッド ++++++++++

    public function GetData($iKind)
    {
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT * FROM v_report WHERE fishkind = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $iKind,
        );
        
        $data = [];
        foreach ($result->getResult() as $row){
            array_push($data, $row);
        }

        return $data;
    }
}